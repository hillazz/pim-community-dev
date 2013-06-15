<?php
namespace Pim\Bundle\ProductBundle\Form\Handler;

use Pim\Bundle\ProductBundle\Entity\ExportProfile;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\FormInterface;

/**
 * Form handler for export profiles
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class ExportProfileHandler
{

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * Constructor for handler
     *
     * @param FormInterface $form    Form called
     * @param Request       $request Web request
     * @param ObjectManager $manager Storage manager
     */
    public function __construct(FormInterface $form, Request $request, ObjectManager $manager)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->manager = $manager;
    }

    /**
     * Process method for handler
     *
     * @param ExportProfile $profile
     *
     * @return boolean
     */
    public function process(ExportProfile $profile)
    {
        $this->form->setData($profile);

        if ($this->request->getMethod() === 'POST') {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($profile);

                return true;
            }
        }

        return false;
    }

    /**
     * Call when form is valid
     *
     * @param ExportProfile $profile
     */
    protected function onSuccess(ExportProfile $profile)
    {
        $this->manager->persist($profile);
        $this->manager->flush();
    }
}
