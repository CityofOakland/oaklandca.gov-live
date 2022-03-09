"use strict";

var indexTemplate = 
	`<article class="py-8 horizontal-card border-gray-300 border-b-2">
		{{#newsImage}}
		<figure class="w-full h-48 sm:h-64 p-4 bg-gray-300 relative z-0 flex items-center justify-center mb-4 lg:mb-0">
			<img src="{{ newsImage }}" class="object-cover absolute h-full w-full inset-0" alt="">
		</figure>
		{{/newsImage}}
		{{^newsImage}}
		<figure class="w-full h-48 sm:h-64 p-4 bg-gray-300 relative z-0 flex items-center justify-center mb-4 lg:mb-0">
		    <div class="text-gray-500 opacity-75 fill-current h-full p-12">
		    	<img src="/dist/img/icon-newspaper.svg" class="h-full" alt="">
		    </div>
		</figure>
		{{/newsImage}}
		<div>
			<h2 class="text-2xl mt-4 sm:mt-0" style="font-weight:600"><a href="{{ url }}">{{{_highlightResult.title.value}}}</a></h2>
			<p class="small my-2 text-gray-700" style="font-size:85%;">{{ formattedDate }}</p>
			<div class="text-gray-1000 mb-4">
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
