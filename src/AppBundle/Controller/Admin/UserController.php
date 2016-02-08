<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 06.02.16
 * Time: 18:30
 */

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\User;
use AppBundle\Form\UserEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class UserController
 * @Route("/admin/users")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends Controller
{
    /**
     * @Route("", name="show_all_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findAll();
        $formManager = $this->get('app.form_manager');

        $deleteForms =[];
        foreach ($users as $user) {
            $deleteForms[$user->getId()] = $formManager->createUserDeleteForm($user)->createView();
        }

        return [
            'users' => $users,
            'deleteForms' => $deleteForms,
        ];
    }

    /**
     * @param User $user
     * @param Request $request
     * @Route("/edit/{id}", name="admin_edit_user")
     * @Template()
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(User $user, Request $request)
    {
        $user->setPlainPassword('password');
        $editForm = $this->createForm(UserEditType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$em->persist($user);
            $em->flush();

            return $this->redirectToRoute('show_all_users');
        }

        return [
            'user' => $user,
            'form' => $editForm->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param User $user
     * @Route("/remove/{id}", name="remove_user")
     * @Method("DELETE")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('app.form_manager')->createUserDeleteForm($user);

        if ('DELETE' == $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->remove($user);
                $em->flush();
            }
        }

        return $this->redirectToRoute('show_all_users');
    }
}