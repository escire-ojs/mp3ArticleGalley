{**
 * plugins/generic/mp3ArticleGalley/display.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Visualización de una galerada MP3.
 *}
<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
{capture assign="pageTitleTranslated"}{translate key="article.pageTitle" title=$article->getLocalizedTitle(null, 'html')|strip_unsafe_html}{/capture}
{include file="frontend/components/headerHead.tpl"}
<body class="pkp_page_{$requestedPage|escape} pkp_op_{$requestedOp|escape}">

	{* Header wrapper *}
	<header class="header_view">

		{capture assign="articleUrl"}{url page="article" op="view" path=$article->getBestId()}{/capture}

		<a href="{$articleUrl}" class="return">
			<span class="pkp_screen_reader">
				{translate key="article.return"}
			</span>
		</a>

		<a href="{$articleUrl}" class="title">
			{$article->getLocalizedTitle(null, 'html')|strip_unsafe_html}
		</a>
	</header>

	<div id="htmlContainer" class="galley_view{if !$isLatestPublication} galley_view_with_notice{/if}" style="overflow:visible;-webkit-overflow-scrolling:touch">
		{if !$isLatestPublication && $galleyPublication}
			<div class="galley_view_notice">
				<div class="galley_view_notice_message" role="alert">
					{translate key="submission.outdatedVersion" datePublished=$galleyPublication->getData('datePublished')|date_format:$dateFormatLong urlRecentVersion=$articleUrl}
				</div>
			</div>
			{capture assign="audioUrl"}
				{url page="article" op="download" path=$article->getBestId()|to_array:'version':$galleyPublication->getId():$galley->getBestGalleyId():$submissionFile->getId() inline=true}
			{/capture}
		{else}
			{capture assign="audioUrl"}
				{url page="article" op="download" path=$article->getBestId()|to_array:$galley->getBestGalleyId():$submissionFile->getId() inline=true}
			{/capture}
		{/if}
		<div class="pkp_mp3_galley_container">
			<audio controls="">
				<source src="{$audioUrl}" type="audio/mpeg">
				{translate key="plugins.generic.mp3ArticleGalley.audioNotSupported"}
			</audio>
		</div>
	</div>

	{call_hook name="Templates::Common::Footer::PageFooter"}
</body>
</html>
