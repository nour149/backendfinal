<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\AccessProject; // Add this line to import the AccessProject e
class UserController extends AbstractController{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    private Security $security;

  
   


#[Route('/api/users/{role}', name: 'detailuser', methods: ['GET'])]
public function getDetailUser(string $role, SerializerInterface $serializer, UserRepository $userRepository): JsonResponse
{
    $user = $userRepository->findOneBy(['role' => $role]);
    
    if ($user) {
        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
}

#[Route('/api/users/{id}', name: 'get_user_role', methods: ['GET'])]
public function getUserRoleByEmail(string $id, UserRepository $userRepository): JsonResponse
{
    $user = $userRepository->findOneBy(['id' => $id]);

    if ($user) {
        dump($user); // This will log the user object
        $role = $user->getRole(); // Assuming the method name is getRole()
        if ($role) {
            return new JsonResponse(['role' => $role], Response::HTTP_OK);
        }
    }

    return new JsonResponse(['error' => 'User not found or role not set'], Response::HTTP_NOT_FOUND);
}

#[Route('/api/users/r', name: 'get_user_emails', methods: ['GET'])]
public function getUserEmails(UserRepository $userRepository): JsonResponse
{
    $users = $userRepository->findAll();
    $emails = [];

    foreach ($users as $user) {
        $emails[] = $user->getEmail();
    }

    return new JsonResponse(['emails' => $emails], Response::HTTP_OK);
}


#[Route('/api/users/', name: 'get_user_roles', methods: ['GET'])]
public function getUserRoles(UserRepository $userRepository): JsonResponse
{
    $users = $userRepository->findAll();     
    $role = [];

    foreach ($users as $user) {
        $role[] = $user->getRole();
    }

    return new JsonResponse(['role' => $role], Response::HTTP_OK);
}

#[Route('/api/users/{email}/role', name: 'get_user_role_by_email', methods: ['GET'])]
public function getUserRoleeeByEmail1(string $email, UserRepository $userRepository): JsonResponse
{
    $user = $userRepository->findOneBy(['email' => $email]);

    if ($user) {
        $role = $user->getRole();
        return new JsonResponse(['role' => $role], Response::HTTP_OK);
    }

    return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
}
    #[Route('/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }


    #[Route('/users', name: 'users', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $userList = $userRepository->findByRole('ROLE_USER');
    
            $formattedUserList = [];
            foreach ($userList as $user) {
                $formattedUserList[] = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                    'is_blocked'=>$user->isIsBlocked()
                
                ];
            }
    
            $jsonUserList = $serializer->serialize($formattedUserList, 'json');
            return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }



    #[Route('/api/users', name: "createUser", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        // Deserialize JSON request body into User object
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        
        // Persist the user to the database
        $em->persist($user);
        $em->flush();

        // Serialize the user into JSON response
        $jsonUser = $serializer->serialize($user, 'json');

        // Return the JSON response
        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }


        #[Route('/block-user/{id}', name: 'block_user', methods: ['POST'])]
        public function blockUser(User $user, EntityManagerInterface $entityManager): JsonResponse
        {
            // Check if the user is not already blocked
            if (!$user->isIsBlocked()) {
                // Block the user
                $user->setIsBlocked(true);
                
                // Persist changes to the database
                $entityManager->flush();
    
                return new JsonResponse(['success' => true, 'message' => 'User blocked successfully'], JsonResponse::HTTP_OK);
            }
    
            return new JsonResponse(['success' => false, 'message' => 'User is already blocked'], JsonResponse::HTTP_BAD_REQUEST);
        }
      
        #[Route('/api/users/send-invitation/{id}', name: 'send_invitation', methods: ['POST'])]
        #[IsGranted('ROLE_ADMIN')]
        public function sendInvitation(User $user, EntityManagerInterface $entityManager): JsonResponse
        {
            // Create a new AccessProject entity for the invitation
            $accessProject = new AccessProject();
            $accessProject->setStatus('pending');
            $accessProject->setRole('user'); // Assuming user role by default
            $accessProject->setCreatedAt(new \DateTime());
            $accessProject->setUpdatedAt(new \DateTime());
            
            // Associate the access project with the user
            $user->addAccessProject($accessProject);
        
            // Persist the new AccessProject entity
            $entityManager->persist($accessProject);
            $entityManager->flush();
        
            // Return success response
            return new JsonResponse(['message' => 'Invitation sent successfully'], Response::HTTP_OK);
        }
        



        #[Route('/users/{id}/invited', name: 'check_invitation', methods: ['GET'])]
        public function checkInvitation(int $id, EntityManagerInterface $entityManager): JsonResponse
        {
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->find($id);
        
            if (!$user) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
        
            // Assuming you have a property or method in your User entity to check if the user is invited
            $isInvited = $user->isInvited();
        
            if ($isInvited) {
                $message = "You have an invitation from admin to work on  a project .";
            } else {
                $message = "You haven't been invited to work on any projects.";
            }
        
            return new JsonResponse(['invited' => $isInvited, 'message' => $message]);
        }
        

        #[Route('/api/users/{id}/invitation', name: 'get_invitation', methods: ['GET'])]
        public function getInvitation(User $user, EntityManagerInterface $entityManager): JsonResponse
        {
            // Retrieve the pending invitation for the user
            $accessProjectRepository = $entityManager->getRepository(AccessProject::class);
            $invitation = $accessProjectRepository->findOneBy(['user' => $user, 'status' => 'pending']);
        
            if (!$invitation) {
                return new JsonResponse(['message' => 'No pending invitation found'], Response::HTTP_NOT_FOUND);
            }
        
            // Return the invitation details
            return new JsonResponse($invitation, Response::HTTP_OK);
        }
        
        #[Route('/api/users/{id}/accept-invitation', name: 'accept_invitation', methods: ['POST'])]
        public function acceptInvitation(User $user, EntityManagerInterface $entityManager): JsonResponse
        {
            // Retrieve the pending invitation for the user
            $accessProjectRepository = $entityManager->getRepository(AccessProject::class);
            $invitation = $accessProjectRepository->findOneBy(['user' => $user, 'status' => 'pending']);
        
            if (!$invitation) {
                return new JsonResponse(['error' => 'No pending invitation found'], Response::HTTP_NOT_FOUND);
            }
        
            // Update the invitation status to accepted
            $invitation->setStatus('accepted');
            $entityManager->flush();
        
            return new JsonResponse(['message' => 'Invitation accepted successfully'], Response::HTTP_OK);
        }
        
        #[Route('/api/users/{id}/refuse-invitation', name: 'refuse_invitation', methods: ['POST'])]
        public function refuseInvitation(User $user, EntityManagerInterface $entityManager): JsonResponse
        {
            // Retrieve the pending invitation for the user
            $accessProjectRepository = $entityManager->getRepository(AccessProject::class);
            $invitation = $accessProjectRepository->findOneBy(['user' => $user, 'status' => 'pending']);
        
            if (!$invitation) {
                return new JsonResponse(['error' => 'No pending invitation found'], Response::HTTP_NOT_FOUND);
            }
        
            // Update the invitation status to refused
            $invitation->setStatus('refused');
            $entityManager->flush();
        
            return new JsonResponse(['message' => 'Invitation refused successfully'], Response::HTTP_OK);
        }
      


        #[Route('/users/{id}/is_blocked', name: 'api_users_is_blocked', methods: ['GET'])]
        public function isBlocked(int $id, EntityManagerInterface $entityManager): JsonResponse
        {
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->find($id);
        
            if (!$user) {
                return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }
        
            if ($user->isIsBlocked()) {
                // Return a JSON response indicating that the user is blocked
                return new JsonResponse(['message' => 'User is blocked'], JsonResponse::HTTP_OK);
            }
        
            // Return a JSON response indicating that the user is not blocked
            return new JsonResponse(['message' => 'User is not blocked'], JsonResponse::HTTP_OK);
        }
        #[Route('/users/{username}', name: 'api_users_username', methods: ['GET'])]
        public function getUserByUsername(string $username, EntityManagerInterface $entityManager): JsonResponse
        {
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->findOneBy(['username' => $username]);
        
            if (!$user) {
                return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }
        
            // Return user details
            return new JsonResponse([
                'id' => $user->getId(),
                // Add other user details as needed
            ]);
        }





        #[Route("/register", name:"app_register", methods: ['POST'])]
    
        public function register(
            Request $request,
            EntityManagerInterface $entityManager,
            UserPasswordHasherInterface $passwordHasher,
            ValidatorInterface $validator,
            MailerInterface $mailer
        ): Response {
            // Parse JSON data from the request body
            $data = json_decode($request->getContent(), true);
        
            // Create a new user instance
            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));        
            // Generate a random validation code
            $validationCode = mt_rand(100000, 999999); // Generate a random 6-digit code
            $user->setValidationCode($validationCode); // Set the validation code for the user
        
            // Validate the user entity
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            }
        
            // Persist the user entity
            $entityManager->persist($user);
            $entityManager->flush();
        
            // Send the validation code to the user's email
            $email = (new Email())
                ->from('nourrachdi15@gmail.com')
                ->to($user->getEmail())
                ->subject('Validation Code for Registration')
                ->text('Your validation code is: ' . $validationCode);
        
            $mailer->send($email);
        
            // Return a success response with the user's ID
            return $this->json([
                'message' => 'User registered successfully. Validation code sent to email.',
                'userId' => $user->getId(),
            ]);
        }














    }