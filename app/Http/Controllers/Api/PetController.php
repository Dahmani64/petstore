<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use  App\Http\Controllers\Controller;
use App\Models\Pet;
use Validator;
class PetController extends Controller
{
    
  
    /**
     * @param Request $request
     * add new Pet
     * @return App\Models\Pet;
     */
    public function store(Request $request)
    {
      $inputs = $request->only('category', 'name', 'photoUrls','tags','status');
      if($pet = Pet::create($inputs) )return response()->json($pet, 200);
    }

    /**
     * @param int $id
     * get Pet by id
     * @return App\Models\Pet| @return Response 404| @return Response  400
     */
    public function getById($id)
    {	
      if((int)$id<1)return response()->json(["message"=>"Invalid ID supplied"],400);
      $pet  = pet::find($id);
      if(!$pet) return response()->json(["message"=>"Pet not found"],404);
      return response()->json($pet,200);
    }

    /**
     * @param Request $request
     * find Pet by status
     * @return array App\Models\Pet| @return Response 404| @return Response  400
     */

    public function findByStatus(Request $request)
    {
      if(array_diff($request->status,['available','pending','sold'])) return response()->json(["message"=>"Invalid status value"],400);
      $pets = Pet::whereIn('status', $request->status)->get();
      return response()->json($pets,200);

    }

  /**
     * @param Request $request
     * update Pet
     * @return array App\Models\Pet | @return Response 404 | @return Response  400
     */
    public function update(Request $request)
    {
       try{
              if((int)$request->id<1)return response()->json(["message"=>"Invalid ID supplied"],400);
              $pet  = pet::find($request->id);
              if(!$pet) return response()->json(["message"=>"Pet not found"],404);
              if($pet->update($request->all()))return response()->json($pet, 200);

          } catch (Exception $e) {
            return response()->json([ 'message' => $e], 405);
          }
    }

     /**
     * @param Request $request 
     * @param int $id 
     * update name and status of Pet
     * @return array App\Models\Pet | @return Response 404 | @return Response  400
     */
    public function updateFormData(Request $request, $id)
    {
          try{
           
                    $array  = $request->all(); $array['id']=$id;
                    $validator = Validator::make($array, [
                      'id' => 'required|integer|min:1',
                      'name' => 'required|between:2,20|string',
                      'status' => 'required|in:available,pending,sold',
                  ]);

                  if($validator->fails()){return response()->json(['Error validation'=> $validator->errors()]);}
                 

                  $pet  = Pet::find($id);
                  if(!$pet) return response()->json(["message"=>"Pet not found"],404);
                 
                  $pet->name = $request->name;$pet->status = $request->status;
                 
                if($pet->save())return response()->json(["message"=>"update success","pet"=>$pet], 200);
               
        } catch (Exception $e) {
          return response()->json([ 'message' => $e], 500);
        }
    }

    /**
     * @param int $id 
     * delete Pet by id
     * @return Response 
     */

     public function destroy($id)
     {

        try{
            if((int)$id<1)return response()->json(["message"=>"Invalid ID supplied"],400);
            $pet  = Pet::find($id);
            if(!$pet) return response()->json(["message"=>"Pet not found"],404);
            
            if($pet->delete()) response()->json(["message"=>"sucess deleted"], 200);

        } catch (Exception $e) { return response()->json([ 'message' => $e], 500);}

     }
    /**
     * @param Request
     * update image of pet
     * @return Response 
     */
     public function uploadImage(Request $request, $id)
     {
          $array  = $request->all();$array['id']=$id;

          $validator = Validator::make($array, [
            'id' => 'required|integer|min:1',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         ]);

        if($validator->fails()){return response()->json(['Error validation'=> $validator->errors()]);}

          $pet  = Pet::find($id);
          if(!$pet) return response()->json(["message"=>"Pet not found"],404);

          if($request->hasFile('file')){ 
              $new_image = $request->file('file')->hashName();
              $request->file('file')->storeAs('pets/'.$pet->id.'/images',$new_image ,'public');  
          }
            return response()->json(["message"=>"successful operation"], 200);
     }
}
