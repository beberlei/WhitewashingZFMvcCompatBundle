<?php

namespace Application\Controller;
use Whitewashing\ZFMvcCompatBundle\Controller\ZendController;

class GuestbookController extends ZendController
{
    public function indexAction()
    {
        $guestbook = new \Application_Model_GuestbookMapper();
        $this->view->entries = $guestbook->fetchAll();
    }

    public function signAction()
    {
        $request = $this->getRequest();
        $form    = new \Application_Form_Guestbook();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $comment = new \Application_Model_Guestbook($form->getValues());
                $mapper  = new \Application_Model_GuestbookMapper();
                $mapper->save($comment);
                return $this->_helper->redirector('index');
            }
        }

        $this->view->form = $form;
    }
}