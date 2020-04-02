<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Form\RegisterFormType;
use Doctrine\Common\Persistence\ObjectManager;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(CategoryRepository $catRepo)
    {
        $categories = $catRepo->selectCategories();

        return $this->render('security/login.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     *
     */
    public function logout(){

    }

    /**
     * @Route("/inscription", name="register")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, CategoryRepository $catRepo){
        $categories = $catRepo->selectCategories();
        $user = new User();
        $form=$this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setProfil(2);
            $user->setImageFile($form['image']->getData());
            $manager->persist($user);
            $manager->flush();
            $mail = $this->sendMail($user, $mailer, "newAccount");
            return $this->render('security/login.html.twig',[
                'form'=> $form->createView(),
                'categories' => $categories
            ]);
        }elseif($form->isSubmitted() && !$form->isValid()){
            $ERROR = "Les informations fournies sont erronés. L'inscription a échoué";
            return $this->render('security/register.html.twig',[
                'form'=> $form->createView(),
                'categories' => $categories,
                'error' => $ERROR
            ]);
        }

        return $this->render('security/register.html.twig',[
            'form'=> $form->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/initPassword", name="initPassword")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function init(Request $request, ObjectManager $manager, UserRepository $uRepo, CategoryRepository $catRepo, \Swift_Mailer $mailer){

        $categories = $catRepo->selectCategories();

        if ($request->get("usernameChange")) {
            $email = $request->get("usernameChange");
            $user = $uRepo->findOneBySomeField($email);
            if ($user != NULL) {
                $token = $this->generateToken('form');
                $user->setToken($token);
                $mail = $this->sendMail($user, $mailer, "changeMDP");
                if($mail != 1){
                    $success = "Un mail vient de vous être envoyé avec la procédure à suivre.";
                    try{
                        $result = $uRepo->updateUser($user);
                    }catch (\Exception $e){
                        $ERROR = "Echec lors de la mise à jour de l'utilisateur";
                        return $this->render('security/initPassword.html.twig', [
                            'error' => $ERROR,
                            'categories' => $categories
                        ]);
                    }
                    return $this->render('security/initPassword.html.twig', [
                        'success' => $success,
                        'categories' => $categories
                    ]);
                }else{
                    $ERROR = "Echec lors de l'envoie";
                    return $this->render('security/initPassword.html.twig', [
                        'error' => $ERROR,
                        'categories' => $categories
                    ]);
                }
            } else {
                $ERROR = "Aucun utilisateur d'inscrit avec cette adresse mail";
                return $this->render('security/initPassword.html.twig', [
                    'error' => $ERROR,
                    'categories' => $categories
                ]);
            }
        }
        return $this->render('security/initPassword.html.twig',[
            'categories' => $categories
        ]);
    }

    function generateToken($form)
    {
        $token = sha1(uniqid(microtime(), true));
        return $token;
    }

    function checkToken($token, UserRepository $uRepo)
    {
        $user = $uRepo->findByExampleField("token", $token);
        $requestDate = $user->getRequest();
        $diff = date_diff($requestDate, new \DateTime());
        $diffMin= $diff->format('%i');
        $diffH= $diff->format('%h');
        $diffJour= $diff->format('%d');
        //check si le token est toujours valable
        if($diffMin > 55 || $diffH >= 1 || $diffJour >= 1){
            $ERROR = "Le lien n'est plus valable. Veuillez réitérer votre requête";
            return $this->render('security/initPassword.html.twig', [
                'error' => $ERROR
            ]);
        }
        //si le token ne correspond à aucun utilisateur
        else if($user == false){
            $ERROR = "Utilisateur inconnue";
            return $this->render('security/initPassword.html.twig', [
                'error' => $ERROR
            ]);
        }else{
            return $user;
        }
    }

    public function sendMail(User $user, $mailer, $request)
    {

        switch ($request) {
            case "changeMDP":
                $adresse ="https://devandmore.yo.fr/change_password?token=".$user->getToken();
                $token = $this->generateToken('form');
                $body = $this->renderView('mail/passwordReset.html.twig', [
                    "adresse" => $adresse
                ]);
                $subject = 'Dev&More Changement de Mot de passe';
                $to = $user->getEmail();
                break;
            case "newAccount" :
                $body = $this->renderView('mail/newAccount.html.twig', [
                    "name" => $user->getSurname(),
                ]);
                $subject = "";
                $to = $user->getEmail();
                break;
            default:
                $body = "Mail par défault";
                 $subject = 'No Life Changement de Mot de passe';
                $from = "admin@devandmore.yo.fr";
                $to = $user->getEmail();
                break;
        }

        $message = (new \Swift_Message('No Life'))
            ->setFrom('admin@devandmore.yo.fr')
            ->setTo($to)
            ->setBody($body,'text/html');

        $result = $mailer->send($message);
    }

    /**
     * @Route("/change_password", name="changePwd")
     * @param Request $request
     * @param UserRepository $uRepo
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function change_password(Request $request, UserRepository $uRepo, UserPasswordEncoderInterface $encoder){

        $request = Request::createFromGlobals();

        if($request->query->get("token") || $request->get("newPwd")){
            //variables
            if($request->get('token')){
                $token = $request->get('token');
            }else{
                $token = $request->query->get("token");
            }
            $user = $this->checkToken($token, $uRepo);
            //En cas de changement de mot de passe
            if ($request->get("newPwd")){
                $checkPassword = strcmp($request->get("newPwd"), $request->get("newPwd2"));
                if($checkPassword === 0 && strlen ($request->get("newPwd"))> 6){
                    $tokenBeforeDelete = $token;
                    $hash = $encoder->encodePassword($user, $request->get("newPwd"));
                    $user->setPassword($hash);
                    $user->setToken(null);
                    try{
                        $result = $uRepo->updateUser($user);
                        return $this->render('security/login.html.twig');
                    }catch (\Exception $e){
                        $ERROR = "Erreur dans la mise à jour";
                        return $this->render('security/change_password.html.twig', [
                            'error' => $ERROR,
                            'token' => $tokenBeforeDelete
                        ]);
                    }
                }else {
                    $ERROR = "Les mots de passe doivent être identiques et doivent faire 6 caractères minimum";
                    return $this->render('security/change_password.html.twig', [
                        'error' => $ERROR,
                        'token' => $token
                    ]);
                }
            }
            return $this->render('security/change_password.html.twig', [
                'token' => $token
            ]);
        }else{
            return $this->render('home/index.html.twig');
        }
    }

}
