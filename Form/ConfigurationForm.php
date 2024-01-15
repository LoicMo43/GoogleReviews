<?php

namespace GoogleReviews\Form;

use GoogleReviews\GoogleReviews;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ConfigurationForm extends BaseForm
{
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add(
                'app_name',
                TextType::class, [
                    'disabled' => true,
                    'required' => false,
                    'label' => Translator::getInstance()->trans('Application Name', [], GoogleReviews::DOMAIN_NAME),
                    'data' => GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_APPLICATION_NAME)
                ]
            )
            ->add(
                'api_key',
                TextType::class, [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('Google Place API Key', [], GoogleReviews::DOMAIN_NAME),
                    'data' => GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_API_KEY)
                ]
            )
            ->add(
                'place_id',
                TextType::class, [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('Place ID:', [], GoogleReviews::DOMAIN_NAME),
                    'data' => GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_PLACE_ID)
                ]
            )
            ->add(
                'cache_duration',
                TextType::class, [
                    'required' => false,
                    'label' => Translator::getInstance()->trans('Cache Duration: (minutes)', [], GoogleReviews::DOMAIN_NAME),
                    'data' => GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_CACHE_DURATION) ?? '10'
                ]
            )
        ;
    }
}