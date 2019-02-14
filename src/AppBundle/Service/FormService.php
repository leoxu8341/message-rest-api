<?php

namespace AppBundle\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * FormService constructor.
     * @param FormFactoryInterface $formFactory
     * @param ValidatorInterface $validator
     */
    public function __construct(FormFactoryInterface $formFactory, ValidatorInterface $validator)
    {
        $this->formFactory = $formFactory;
        $this->validator = $validator;
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
        $errors = $this->validator->validate($object);

        /**@var \Symfony\Component\Validator\ConstraintViolation $error * */
        foreach ($errors as $error) {
            $errordata[$error->getPropertyPath()] = $error->getMessage();
        }

        if (!empty($errordata)) {
            throw new BadRequestHttpException(json_encode($errordata, true));
        }
    }
}
