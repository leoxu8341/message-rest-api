<?php

namespace AppBundle\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class FormService.
 */
class FormService
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * FormService constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param $data
     * @param $object
     * @param $formClass
     */
    public function postForm(
        $data,
        $object,
        $formClass
    ) {
        $form = $this->formFactory->create($formClass, $object);
        $form->submit($data, false);

        $errordata = [];
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            $errordata = [
                'type' => 'validation_error',
                'title' => 'There was a validation error',
                'errors' => $errors
            ];
        }

        if (!empty($errordata)) {
            throw new BadRequestHttpException($errordata);
        }
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
