<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\Dance;
use App\Entity\Step;
use App\Entity\Stepsequence;
use App\Repository\StepsequenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class StepsequenceStepController extends AbstractController
{

    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }
    #[Route('/stepsequence/add/file', name: 'app_add_file', methods: ['POST'])]
    public function newFile(Request $request,EntityManagerInterface $entityManager) :JsonResponse
    {
        $projectDir = $this->params->get('kernel.project_dir');
        $data = json_decode($request->getContent(), true);
        if (!isset($data['dance_id'], $data['badge_id'], $data['name'], $data['steps'],$data["difficulty"])) {
            return new JsonResponse(['error' => 'Missing required fields', "msg" => $data], 400);
        }
        $dance = $entityManager->getRepository(Dance::class)->find($data['dance_id']);
        $badge = $entityManager->getRepository(Badge::class)->find($data['badge_id']);

        if (!$dance || !$badge) {
            return new JsonResponse(['error' => 'Invalid dance_id or badge_id'], 400);
        }

        $dir = $projectDir."/src/DataFixtures/data/stepsequences/";

        if (!is_dir($dir)) {
            return new JsonResponse(["error" => "path doesnt exit", "path" =>$dir],400);
        }

        $file = fopen($dir.str_replace(' ', '', $data['name']).".json","w");
        fwrite($file,$request->getContent());

        fclose($file);

        return new JsonResponse(["status" => "successfully created file"]);
    }
    #[Route('/stepsequence/add', name: 'app_stepsequence_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['dance_id'], $data['badge_id'], $data['name'], $data['steps'],$data["difficulty"])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $stepsequence = new Stepsequence();
        $dance = $entityManager->getRepository(Dance::class)->find($data['dance_id']);
        $badge = $entityManager->getRepository(Badge::class)->find($data['badge_id']);

        if (!$dance || !$badge) {
            return new JsonResponse(['error' => 'Invalid dance_id or badge_id'], 400);
        }
        $stepsequence->setDance($dance);
        $stepsequence->setBadge($badge);
        $stepsequence->setName($data["name"]);
        $stepsequence->setDifficulty($data["difficulty"]);


        foreach ($data['steps'] as $stepData) {
            $step = new Step();

            $step->setHowquick($stepData['howquick']);

            $step->setM1X($stepData['m1_x']);
            $step->setM1Y($stepData['m1_y']);
            $step->setM1Rotate($stepData['m1_rotate']);
            $step->setM1Heel($stepData['m1_heel']);
            $step->setM1Toe($stepData['m1_toe']);

            $step->setM2X($stepData['m2_x']);
            $step->setM2Y($stepData['m2_y']);
            $step->setM2Rotate($stepData['m2_rotate']);
            $step->setM2Heel($stepData['m2_heel']);
            $step->setM2Toe($stepData['m2_toe']);

            $step->setW1X($stepData['w1_x']);
            $step->setW1Y($stepData['w1_y']);
            $step->setW1Rotate($stepData['w1_rotate']);
            $step->setW1Heel($stepData['w1_heel']);
            $step->setW1Toe($stepData['w1_toe']);

            $step->setW2X($stepData['w2_x']);
            $step->setW2Y($stepData['w2_y']);
            $step->setW2Rotate($stepData['w2_rotate']);
            $step->setW2Heel($stepData['w2_heel']);
            $step->setW2Toe($stepData['w2_toe']);

            $stepsequence->addHowquick($step);

            $entityManager->persist($step);

        }

        $entityManager->persist($stepsequence);
        $entityManager->flush();
        return new JsonResponse(['status' => 'Dance and steps saved'], 201);
    }

    #[Route('/stepsequence/get/{id}', name: 'app_step_byId', methods: ['GET'])]
    public function getStepsequenceByID(SerializerInterface $serializer, Stepsequence $stepsequence) :JsonResponse
    {

        $response = $serializer->serialize(
            $stepsequence,
            'json',
            ['groups' => ['stepsequence:read']]
        );
        return JsonResponse::fromJsonString($response);
    }
    #[Route('/stepsequence/get', name: 'app_step_get', methods: ['GET'])]
    public function getStepsequences(SerializerInterface $serializer, StepsequenceRepository $stepsequenceRepository) :JsonResponse
    {
        $stepsequences = $stepsequenceRepository->findAll();
        $response = $serializer->serialize(
            $stepsequences,
            'json',
            ['groups' => ['stepsequence:read']]
        );
        return JsonResponse::fromJsonString($response);


    }

    #[Route('/stepsequence/badge/{id}', name: 'app_step_get_by_badge', methods: ['GET'])]
    public function getStepsequencesByBadge(SerializerInterface $serializer, Badge $badge) :JsonResponse
    {
        $response = $serializer->serialize(
            $badge->getStepsequences(),
            'json',
            ['groups' => ['stepsequence:read']]
        );
        return JsonResponse::fromJsonString($response);
    }

    #[Route('/stepsequence/dance/{id}', name: 'app_step_get_by_dance', methods: ['GET'])]
    public function getStepsequencesByDance(SerializerInterface $serializer, Dance $dance) :JsonResponse
    {
        $response = $serializer->serialize(
            $dance->getStepsequences(),
            'json',
            ['groups' => ['stepsequence:read']]
        );
        return JsonResponse::fromJsonString($response);
    }

}
