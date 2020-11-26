<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'security_login'
            && $request->isMethod("POST");
    }

    // getCredentials() est appelée si la fonction supports() renvoie true
    public function getCredentials(Request $request)
    {
        return $request->request->get('login'); //array avec ici 3 infos
    }

    // $credentials vient de getCredentials() au-dessus
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $userProvider->loadUserByUsername($credentials['email']); //le userProvider est "décrit" dans security.yaml => provider
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException("l'adresse email \"" . $credentials['email'] . "\" n'existe pas !");
        }
    }

    // $user vient de la fonction getUser() au-dessus
    public function checkCredentials($credentials, UserInterface $user)
    {
        $isValid = $this->encoder->isPasswordValid($user, $credentials['password']);
        if (!$isValid) {
            throw new AuthenticationException("le mot de passe est incorrect !");
        }
        
        return $isValid;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $email = $request->request->get('login')['email'];
        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);
        $request->attributes->set(Security::LAST_USERNAME, $email);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse('/');
    }

    // Cette méthode est appelée lorsque l'on veut acceder à une ressource qui nécessite une authentification (ex : access_control renseigné)
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
