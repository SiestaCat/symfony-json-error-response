<?php

namespace Siestacat\SymfonyJsonErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormErrorIterator;

/**
 * @package Siestacat\SymfonyJsonErrorResponse
 */
abstract class JsonErrorResponse extends AbstractController
{
    protected function json_error(\Exception $e, ?LoggerInterface $logger = null, array $json = []):JsonResponse
    {
        if($logger !== null) $logger->error($e->getMessage());

        $error_message = !($this->getParameter('kernel.environment') === 'prod') ? $e->getMessage() . "\n" . $e->getTraceAsString() : null;

        return $this->json_error_message($error_message, $json);
    }

    protected function json_error_message(?string $error_message, array $json = []):JsonResponse
    {
        $json['error'] = $error_message;

        $json['success'] = false;

        return $this->json($json)->setStatusCode(500);
    }

    protected function json_error_form(FormErrorIterator $form_errors, array $json = []):JsonResponse
    {
        foreach($form_errors as $form_error)
        {
            return $this->json_error_message($form_error->getMessage(), $json);
        }
    }
}