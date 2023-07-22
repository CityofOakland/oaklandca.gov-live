"use strict";

var indexTemplate = `
<div class="block">
    <div class="text-gray-1000 md:flex md:items-start md:gap-6">
        <figure class="block h-12 w-12 md:h-auto md:w-auto md:flex-1 aspect-[1/1] overflow-hidden border-2 rounded-full bg-gray-100 border-gray-200">
            {{#portrait}}
                <img src="{{ portrait }}" alt="Portrait of {{ jobTitle }}, {{ title }}" class="object-cover h-full w-full">
            {{/portrait}}
            {{^portrait}}
                <div class="opacity-75 fill-current text-gray-400 bg-gray-100 border-gray-200">
                    <img src="/dist/img/icon-user.svg" class="h-full block fill-current" alt="">
                </div>
            {{/portrait}}
        </figure>
        <div class="pt-4 md:pt-0 md:w-3/4">
            <h2 class="m-0 text-2xl">
                <a aria-label="{{ title }}, {{jobTitle }}" href="{{ url }}">
                    {{ title }}
                </a>
            </h2>
            <p class="mt-2">
                {{ jobTitle }}
                {{#department}}<br>{{ department }}{{/department}}
                {{#email}}<br><a href="mailto:{{ email }}" aria-label="Email {{ title }} at {{ email }}">Email</a>{{/email}}
                {{#phone}}<br><a href="tel:{{ phone }}" aria-label="Call {{ title }} at {{ phone }}">{{ phone }}</a>{{/phone}}
            </p>
        </div>
    </div>
</div>
`;