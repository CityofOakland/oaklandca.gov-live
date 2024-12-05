<?php

namespace modules\jobs;

use Craft;
use craft\elements\User;
use craft\queue\BaseJob;
use craft\queue\Queue;
use Illuminate\Support\Collection;
use yii\queue\JobInterface;

class SuspendExpiredUsers extends BaseJob implements JobInterface
{
    /**
     * @throws \DateMalformedStringException
     */
    public function execute($queue): void
     {
         Craft::info('Running User Suspension job', __METHOD__);

         $users = $this->getInactiveUsers();
         $totalUsers = $users->count();

         foreach ($users->all() as $i => $user) {
             try {
                 Craft::$app->users->suspendUser($user);
             } catch (\Throwable $e) {
                 Craft::error("Failed to suspend user: " . $e->getMessage(), __METHOD__);
             }

             $this->setProgress(
                 $queue,
                 $i / $totalUsers,
                 Craft::t('app', '{step, number} of {total, number}', [
                     'step' => $i + 1,
                     'total' => $totalUsers,
                 ])
             );
         }

         Craft::info('User suspension job complete!', __METHOD__);
     }

    protected function defaultDescription(): ?string
    {
        return Craft::t('app', 'Suspending expired users');
    }

    /**
     * Suspend users who haven't logged in for the last 6 months.
     * @throws \DateMalformedStringException
     */
    private function getInactiveUsers(): Collection
    {
        $sixMonthsAgo = (new \DateTime())->modify('-6 months')->format('Y-m-d H:i:s');

        // Find all users who haven't logged in for more than 6 months
        $inactiveUsers = User::find()
            ->lastLoginDate("<= $sixMonthsAgo")
            ->status([User::STATUS_ACTIVE])
            ->orderBy(['id' => SORT_ASC]);

        // Users who never cared to activate their account
        $staleUsers = User::find()
            ->dateCreated("<= $sixMonthsAgo")
            ->status([User::STATUS_PENDING])
            ->orderBy(['id' => SORT_ASC]);

        // Users accounts that are set to expire
        $expiredUsers = User::find()
            ->expiresOn('<= ' . date('Y-m-d'))
            ->orderBy(['id' => SORT_ASC]);

        Craft::warning([$inactiveUsers->all(), $staleUsers->all(), $expiredUsers->all()]);

        $allUsers = array_merge(
            $inactiveUsers->all(),
            $staleUsers->all(),
            $expiredUsers->all(),
        );

        return collect($allUsers)->unique('id');
    }

    public static function isJobAlreadyQueued(Queue $queue): bool
    {
        $queueJobs = $queue->getJobInfo();
        foreach ($queueJobs as $job) {
            if (isset($job['class']) && $job['class'] === static::class) {
                return true;
            }
        }
        return false;
    }
}
