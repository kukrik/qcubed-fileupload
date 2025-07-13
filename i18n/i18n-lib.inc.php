<?php
/**
 * Translation shortcuts specific to this library
 */

namespace QCubed\Plugin; // include this file in your namespace so that the functions defined below are unique to your library

    use QCubed\I18n\TranslationService;
    
    const I18N_DOMAIN = 'kukrik/qcubed-fileupload'; // replace this with your package name

    /**
    * Translation function specific to your package.
    *
    * @param string $strMsgId   String to translate
    * @param string|null $strContext    Context string, if the same string gets translated in different ways depending on context
    *
    * @return string
    */
    function t(string $strMsgId, ?string $strContext = null): string
    {
        if (class_exists("\\QCubed\\I18n\\TranslationService") && TranslationService::instance()->translator()) {
            if (!defined (I18N_DOMAIN . '__BOUND')) {
                define(I18N_DOMAIN . '__BOUND', 1);
                TranslationService::instance()->bindDomain(I18N_DOMAIN, __DIR__);	// bind the directory containing the .po files to my domain
            }
            return TranslationService::instance()->translate($strMsgId, I18N_DOMAIN, $strContext);
        }
        
    return $strMsgId;
        
    }

    /**
     * Translation function for plural translations.
     *
     * @param string $strMsgId  Singular string
     * @param string $strMsgId_plural   Plural string
     * @param 
     * @param string|null $strContext   Context if needed
     *
     * @return string
     */
    function tp(string $strMsgId, string $strMsgId_plural, int $intNum, ?string $strContext = null)
    {
        return extracted($strMsgId, $strMsgId_plural, $intNum, $strContext);
    }

/**
 * @param string $strMsgId
 * @param string $strMsgId_plural
 * @param int $intNum
 * @param string|null $strContext
 *
 * @return string
 */
function extracted(string $strMsgId, string $strMsgId_plural, int $intNum, ?string $strContext): string
{
    if (class_exists("\\QCubed\\I18n\\TranslationService") && TranslationService::instance()->translator()) {
        if (!defined(I18N_DOMAIN . '__BOUND')) {
            define(I18N_DOMAIN . '__BOUND', 1);
            TranslationService::instance()->bindDomain(I18N_DOMAIN, __DIR__);    // bind the directory containing the .po files to my domain

        }
        return TranslationService::instance()->translatePlural(
            $strMsgId,
            $strMsgId_plural,
            $intNum,
            I18N_DOMAIN,
            $strContext);
    }
    if ($intNum == 1) {
        return $strMsgId;
    } else {
        return $strMsgId_plural;
    }
}