"use strict";

var indexTemplate = 
	`<article class="border-b-2 border-gray-300 py-8 horizontal-card">
		{{#newsImage}}
		<figure class="relative z-0 mb-4 flex h-48 w-full items-center justify-center bg-gray-300 p-4 sm:h-64 lg:mb-0">
			<img src="{{ newsImage }}" class="absolute inset-0 h-full w-full object-cover" alt="">
		</figure>
		{{/newsImage}}
		{{^newsImage}}
		<figure class="relative z-0 mb-4 flex h-48 w-full items-center justify-center bg-gray-300 p-4 sm:h-64 lg:mb-0">
		    <div class="h-full fill-current p-12 text-gray-500 opacity-75">
		    	<img src="/dist/img/icon-newspaper.svg" class="h-full" alt="">
		    </div>
		</figure>
		{{/newsImage}}
		<div>
			<h2 class="mt-4 text-2xl sm:mt-0" style="font-weight:600"><a href="{{ url }}">{{{_highlightResult.title.value}}}</a></h2>
			<p class="my-2 text-gray-700 small" style="font-size:85%;">{{ formattedDate }}</p>
			<div class="mb-4 text-gray-1000">
				{{#summary}}
					{{{_highlightResult.summary.value}}}
				{{/summary}}
				{{^summary}}
					{{{_snippetResult.body.value}}}{{^_snippetResult.body}}\u2026{{/_snippetResult.body}}
				{{/summary}}
			</div>
			<div>
				<a aria-label="Continue reading {{ title }}" href="{{ url }}">Read More \xBB</a>
			</div>
		</div>
	</article>`;
