<?php


namespace App\Security;

use App\Repository\UtilisateurRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator ;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface ;

class AppGithub extends SocialAuthenticator implements LogoutSuccessHandlerInterface
{
    /** @var RouterInterface */
    private $router ;
    private $clientRegistry ;
    private $utilisateurRepository ;
    private $loginroute ;
    private $encoder;
        function __construct(UserPasswordEncoderInterface $encoder,RouterInterface $router ,ClientRegistry $clientRegistry ,UtilisateurRepository $utilisateurRepository){
$this->router=$router ;
$this->clientRegistry=$clientRegistry;
$this->utilisateurRepository = $utilisateurRepository ;
            $this->encoder = $encoder;

    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('app_loginf'));
    }

    public function supports(Request $request)
    {
        $this->loginroute = $request->attributes->get('_route') ;
        return 'oauth_check' === $request->attributes->get('_route');
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->clientRegistry->getClient('github'));
    }

    /**
     * @param AccessToken $credentials
     */

    public function getUser($credentials, UserProviderInterface $userProvider)
    {/** @var GithubResourceOwner $user */
        $user = $this->clientRegistry->getClient('github')->fetchUserFromToken($credentials);
        $response = HttpClient::create()->request(
            'GET',
            'https://api.github.com/user/emails',
            [
                'headers'=> [
                    'authorization'=>"token {$credentials->getToken()}"
                ]
            ]
        );
        $emails = json_decode($response->getContent(),true);
        foreach ($emails as $email) {
            if ( $email['primary']=== true && $email['verified'] === true){
                $data = $user->toArray();
                $data['email']=$email['email'];
                $user = new GithubResourceOwner($data);
            }
        }

        if ($user->getEmail() === null){
           throw new CustomUserMessageAuthenticationException("ce mail ne semble pas verfié");
        }
       return $this->utilisateurRepository->findOrCreateFromGithubOauth($user,$this->encoder);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('app_loginf'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse('\accueil');
    }

    public function onLogoutSuccess(Request $request)
    {
        return new RedirectResponse($this->urlGenerator->generate("loginf"));
    }


}