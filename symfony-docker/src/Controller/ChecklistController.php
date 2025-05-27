<?php

namespace App\Controller;

use App\Entity\Checklist;
use App\Entity\Stepsequence;
use App\Entity\User;
use App\Repository\ChecklistRepository;
use App\Repository\StepsequenceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ChecklistController extends AbstractController
{


    //Setters
    #[IsGranted('ROLE_USER')]
    #[Route("/checklist/new", name: 'app_checklist_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        if (!isset($data['name'], $data['stepsequences'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }


        $checklist = new Checklist();
        foreach ($data["stepsequences"] as $stepsequenceID) {
            $stepsequence = $entityManager->getRepository(Stepsequence::class)->find($stepsequenceID);
            if ($stepsequence) {
                $checklist->addStepsequence($stepsequence);
            } else {
                return new JsonResponse(["error" => "a stepsequence ID cant be found in the database"]);
            }

        }


        $checklist->setName($data['name']);
        $checklist->setUserId($user);

        $entityManager->persist($checklist);
        $entityManager->flush();


        return new JsonResponse(["status" => "successfully created checklist"], 201);
    }

    #[IsGranted('ROLE_USER')]
    #[Route("/checklist/clone", name: 'app_checklist_clone', methods: ['POST'])]
    public function clone(Request $request, EntityManagerInterface $entityManager, ChecklistRepository $checklistRepository): JsonResponse
    {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        if (!isset($data['checklist_id'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $checklist = $checklistRepository->find($data['checklist_id']);

        $newChecklist = new Checklist();

        $newChecklist->setName($checklist->getName());
        $newChecklist->setUserId($user);

        foreach ($checklist->getStepsequences() as $stepsequence) {
            $newChecklist->addStepsequence($stepsequence);
        }

        $entityManager->persist($newChecklist);
        $entityManager->flush();


        return new JsonResponse(["status" => "successfully created checklist"]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route("/checklist/add/stepsequence", name: 'app_checklist_add', methods: ['POST'])]
    public function add(Request $request, ChecklistRepository $checklistRepository, EntityManagerInterface $entityManager, StepsequenceRepository $stepsequenceRepository): JsonResponse
    {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        if (!isset($data['checklist_id'], $data['stepsequence_id'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $checklist = $checklistRepository->find($data['checklist_id']);
        if (!$checklist) {
            return new JsonResponse(['error' => 'Checklist not found'], 404);
        }
        $stepsequence = $stepsequenceRepository->find($data['stepsequence_id']);
        if (!$stepsequence) {
            return new JsonResponse(['error' => 'Stepsequence not found'], 404);
        }
        if($checklist->getUserId()->getEmail() !== $user->getUserIdentifier()){
            return new JsonResponse(['error' => 'You cant add a stepsequence to a checklist that is not yours'], 403);
        }

        $checklist->addStepsequence($stepsequence);
        $entityManager->flush();


        return new JsonResponse(["status" => "successfully created checklist"]);
    }


    // Getters
    #[IsGranted('ROLE_USER')]
    #[Route("/checklist/user/get", name: 'app_checklist_user', methods: ['GET'])]
    public function getUserChecklists(SerializerInterface $serializer, UserRepository $userRepository): JsonResponse
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $jwtUser = $this->getUser();

        $user = $userRepository->findOneBy(['email' => $jwtUser->getUserIdentifier()]);

        $response = $serializer->serialize(
            $user->getChecklists(),
            "json",
            ['groups' => ['checklist:read']]
        );


        return JsonResponse::fromJsonString($response);
    }

    #[Route("/checklist/get", name: 'app_checklist_get', methods: ['GET'])]
    public function getChecklists(SerializerInterface $serializer, ChecklistRepository $checklistRepository): JsonResponse
    {


        $checklist = $checklistRepository->findAll();

        $response = $serializer->serialize(
            $checklist,
            "json",
            ['groups' => ['checklist:read']]
        );


        return JsonResponse::fromJsonString($response);
    }

    #[Route("/checklist/get/{id}", name: 'app_checklist_get_id', methods: ['GET'])]
    public function getChecklistById(SerializerInterface $serializer, Checklist $checklist): JsonResponse
    {
        try {
            $response = $serializer->serialize(
                $checklist,
                "json",
                ['groups' => ['checklist:read']]
            );
        } catch (NotFoundHttpException $e) {
            return new JsonResponse(['error' => 'Checklist not found'], 404);
        }


        return JsonResponse::fromJsonString($response);
    }


    // deleters

    #[Route("/checklist/delete/stepsequence", name: 'app_checklist_delete_stepsequence', methods: ['POST'])]
    public function deleteStepsequence(Request $request, EntityManagerInterface $entityManager, ChecklistRepository $checklistRepository, StepsequenceRepository $stepsequenceRepository): JsonResponse
    {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        if (!isset($data['checklist_id'], $data['stepsequence_id'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $checklist = $checklistRepository->find($data['checklist_id']);
        if (!$checklist) {
            return new JsonResponse(['error' => 'Checklist not found'], 404);
        }
        $stepsequence = $stepsequenceRepository->find($data['stepsequence_id']);
        if (!$stepsequence) {
            return new JsonResponse(['error' => 'Stepsequence not found'], 404);
        }

        if($checklist->getUserId()->getEmail() !== $user->getUserIdentifier()){
            return new JsonResponse(['error' => 'You cant delete a stepsequence from a checklist that is not yours'], 403);
        }
        $checklist->removeStepsequence($stepsequence);
        $entityManager->flush();


        return new JsonResponse(['status' => 'Stepsequence succesfully removed from Checklist'], 200);

    }

    #[Route("/checklist/delete", name: 'app_checklist_delete', methods: ['POST'])]
    public function delete(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, ChecklistRepository $checklistRepository, StepsequenceRepository $stepsequenceRepository): JsonResponse
    {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $jwtUser = $this->getUser();

        $data = json_decode($request->getContent(), true);
        if (!isset($data['checklist_id'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $checklist = $checklistRepository->find($data['checklist_id']);
        if (!$checklist) {
            return new JsonResponse(['error' => 'Checklist not found'], 404);
        }

        $user = $userRepository->findOneBy(['email' => $jwtUser->getUserIdentifier()]);

        if($checklist->getUserId()->getEmail() !== $user->getUserIdentifier()){
            return new JsonResponse(['error' => 'You cant delete a checklist that is not yours'], 403);
        }


        $user->removeChecklist($checklist);
        $entityManager->remove($checklist);
        $entityManager->flush();




        return new JsonResponse(['status' => 'Stepsequence succesfully removed from Checklist'], 200);

    }


}


