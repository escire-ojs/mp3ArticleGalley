<?php

/**
 * @file plugins/generic/mp3ArticleGalley/Mp3ArticleGalleyPlugin.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Mp3ArticleGalleyPlugin
 *
 * @ingroup plugins_generic_mp3ArticleGalley
 *
 * @brief Plugin para visualización de galeradas de artículo en MP3.
 */

namespace APP\plugins\generic\mp3ArticleGalley;

use APP\template\TemplateManager;
use PKP\plugins\Hook;
use PKP\plugins\GenericPlugin;

class Mp3ArticleGalleyPlugin extends GenericPlugin
{
    /**
     * @see Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null)
    {
        if (!parent::register($category, $path, $mainContextId)) {
            return false;
        }
        if ($this->getEnabled($mainContextId)) {
            Hook::add('ArticleHandler::view::galley', $this->articleViewCallback(...), Hook::SEQUENCE_LATE);
        }
        return true;
    }

    /**
     * @copydoc Plugin::getContextSpecificPluginSettingsFile()
     */
    public function getContextSpecificPluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName()
    {
        return __('plugins.generic.mp3ArticleGalley.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription()
    {
        return __('plugins.generic.mp3ArticleGalley.description');
    }

    /**
     * Presenta la página de visualización de la galerada MP3.
     *
     * @param string $hookName
     * @param array $args
     *
     * @return bool
     */
    public function articleViewCallback($hookName, $args)
    {
        $request = &$args[0];
        $issue = &$args[1];
        /** @var \PKP\galley\Galley $galley */
        $galley = &$args[2];
        $article = &$args[3];

        if ($galley && in_array($galley->getFileType(), ['audio/mpeg', 'audio/mp3', 'audio/mpeg3', 'audio/x-mpeg'])) {
            /** @var ?\APP\publication\Publication $galleyPublication */
            $galleyPublication = null;
            foreach ($article->getData('publications') as $publication) {
                if ($publication->getId() === $galley->getData('publicationId')) {
                    $galleyPublication = $publication;
                    break;
                }
            }

            $templateMgr = TemplateManager::getManager($request);
            $templateMgr->addStyleSheet(
                'mp3ArticleGalley',
                $request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/mp3Galley.css',
                ['contexts' => ['frontend']]
            );
            $templateMgr->assign([
                'issue' => $issue,
                'article' => $article,
                'galley' => $galley,
                'isLatestPublication' => $article->getData('currentPublicationId') === $galley->getData('publicationId'),
                'galleyPublication' => $galleyPublication,
                'submissionFile' => $galley->getFile(),
            ]);
            $templateMgr->display($this->getTemplateResource('display.tpl'));

            return true;
        }

        return false;
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\mp3ArticleGalley\Mp3ArticleGalleyPlugin', '\Mp3ArticleGalleyPlugin');
}
