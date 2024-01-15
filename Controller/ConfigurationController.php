<?php

namespace GoogleReviews\Controller;

use Exception;
use GoogleReviews\Form\ConfigurationForm;
use GoogleReviews\GoogleReviews;
use GoogleReviews\Service\GoogleApiService;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;

#[Route(path: "/admin/module/GoogleReviews", name: "admin_google_reviews")]
class ConfigurationController extends BaseAdminController
{
    #[Route('/configuration', name: 'configuration', methods: 'POST')]
    public function saveConfiguration(GoogleApiService $service, ParserContext $parserContext): RedirectResponse|Response
    {
        $form = $this->createForm(ConfigurationForm::getName());
        try {
            $data = $this->validateForm($form)->getData();

            GoogleReviews::setConfigValue(GoogleReviews::GOOGLE_API_KEY, $data["api_key"]);
            GoogleReviews::setConfigValue(GoogleReviews::GOOGLE_PLACE_ID, $data["place_id"]);
            GoogleReviews::setConfigValue(GoogleReviews::GOOGLE_CACHE_DURATION, $data["cache_duration"] ?? 10);

            if (null === $content = $service->checkValidConfiguration()) {
                GoogleReviews::setConfigValue(GoogleReviews::GOOGLE_APPLICATION_NAME, '');

                $msg = Translator::getInstance()->trans('Configuration is not valid', [], GoogleReviews::DOMAIN_NAME);

                throw new RuntimeException($msg);
            }

            GoogleReviews::setConfigValue(GoogleReviews::GOOGLE_APPLICATION_NAME, $content['displayName']['text']);

            return $this->generateSuccessRedirect($form);

        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        } catch (Exception|RuntimeException $e) {
            $error_message = $e->getMessage();
        }

        $form->setErrorMessage($error_message);

        $parserContext
            ->addForm($form)
            ->setGeneralError($error_message);

        return $this->generateErrorRedirect($form);
    }
}