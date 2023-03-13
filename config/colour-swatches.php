<?php
/**
 * color-swatches plugin for Craft CMS 3.x.
 *
 * Let clients choose from a predefined set of colours.
 *
 * @link      https://percipio.london
 *
 * @copyright Copyright (c) 2020 Percipio.London
 */

return [

// Custom  palettes, fixed options [label, default (boolean), colour (array(colour, customOptions)) ]
		'palettes' => [
				'Spotlight Backgrounds' => [
						[
								'label'   => 'Light Yellow',
								'default' => true,
								'color'   =>  [
										[
												'color'             => '#fffbea',
												'background'        => 'bg-yellow-100',
												'text'              => 'text-gray-1000'
										]
								]
						],
						[
								'label'   => 'Light Gray',
								'default' => false,
								'color'   =>  [
										[
												'color'             => '#f7f7f7',
												'background'        => 'bg-gray-100',
												'text'              => 'text-gray-1000'
										]
								]
						],
						[
								'label'   => 'Light Blue',
								'default' => false,
								'color'   =>  [
										[
												'color'             => '#f0f4f8',
												'background'        => 'bg-blue-100',
												'text'              => 'text-gray-1000'
										]
								]
						],
						[
								'label'   => 'Dark Blue',
								'default' => false,
								'color'   =>  [
										[
												'color'             => '#334e68',
												'background'        => 'bg-blue-800',
												'text'              => 'text-white'
										]
								]
						],
						[
								'label'   => 'White',
								'default' => false,
								'color'   =>  [
										[
												'color'             => '#fff',
												'background'        => 'bg-white',
												'text'              => 'text-gray-1000'
										]
								]
						],
				],
				'Cards' => [  // custom label
						[
								'label'   => 'Dark Green',
								'default' => true,
								'color'   =>  [
										[
												'color'             => '#207127',
												'background'        => 'bg-green-800',
												'backgroundHover'   => 'hover:bg-green-600',
												'text'              => 'text-white',
												'textHover'         => 'hover:text-white group-hover:text-white',
												'link'              => 'text-white',
												'linkHover'         => 'hover:text-white group-hover:text-white'
										]
								]
						],
						[
								'label'   => 'White',
								'default' => false,
								'color'   =>  [
										[
												'color'             => '#ffffff',
												'background'        => 'bg-white',
												'backgroundHover'   => 'hover:bg-gray-100',
												'text'              => 'text-gray-1000',
												'textHover'         => 'hover:text-gray-1000 group-hover:text-gray-1000',
												'link'              => 'text-green-800',
												'linkHover'         => 'hover:text-green-600 group-hover:text-green-600'
										]
								]
						],
				]
		]

];