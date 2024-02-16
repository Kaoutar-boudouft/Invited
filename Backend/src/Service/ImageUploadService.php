<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

class ImageUploadService
{
    private $params;

    public function __construct(ParameterBagInterface $params){
        $this->params = $params;
    }

    public function upload(UploadedFile $picture, ?string $folder='') : JsonResponse{

        //rename file
        $file = md5(uniqid(rand(), true)) . '.jpeg';

        //get picture info
        $pictureInfo = getimagesize($picture);
        if ($pictureInfo === false){
            return new JsonResponse([
                'status' => 201,
                'msg' => "incorrect image format !"
            ]);
        }

        $path = $this->params->get('images_directory') . $folder;

        //create folder if not exist
        if (!file_exists($path)){
            mkdir($path ,0755 ,true);
        }

        $picture->move($path . '/' ,$file);

        return new JsonResponse([
            'status' => 200,
            'data' => $file
        ]);

    }

    public function destroy(string $file ,?string $folder = '' ) : JsonResponse{
        if ($file !== 'default.png'){
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $original = $path . '/' . $file;
            if (file_exists($original)){
                unlink($original);
                $success = true;
            }

            if ($success){
                return new JsonResponse([
                    'status' => 200,
                    'msg' => 'image removed with success !'
                ]);
            }

            else {
                return new JsonResponse([
                    'status' => 201,
                    'msg' => 'error when trying to remove image !'
                ]);
            }
        }

        else {
            return new JsonResponse([
                'status' => 201,
                'msg' => 'you try to remove default image !'
            ]);
        }
    }

}