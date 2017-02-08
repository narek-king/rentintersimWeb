<?php
/**
 * Created by PhpStorm.
 * User: narek
 * Date: 11/23/16
 * Time: 10:15 AM
 */

namespace Rentintersimrepo\users;

use App\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use DB;


class UserManager
{
    public function getMyNetwork($id){
        $users = User::select('id', 'login', 'level', 'type', 'supervisor_id', 'name')->orderBy('level', 'asc')->get()->toArray();
        $network = $this->buildTree($this->solveUsers($users), $id);
//        echo '<pre/>';
//        print_r($network);
//        die();
        $final = User::find($id)->toArray();
        $final = $this->solveUsers(array($final))[0];
//        dd($final);
        $final['child'] = $network;
        return [$final];
    }
    public function getMyFlatNetwork($id){
        $users = User::select('id', 'login', 'level', 'type', 'supervisor_id', 'name')->orderBy('level', 'asc')->get()->toArray();
        $network = $this->buildTree($this->solveUsers($users), $id);
        $flat = $this->flatten($network);

//        echo '<pre/>';
//        print_r($flat);
//        die();
        $final = $this->solveUsers(array(User::find($id)->toArray()));
//        dd($flat);
        $final = array_merge($final, $flat);
//        dd($final);
        return $final;
//            return $network;
    }
    function buildTree($elements, $parentId = 0)
    {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['supervisor_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['child'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    function flatten($element, $flatArray=[])
    {
        if (!array_key_exists('child', $element) && $this->isAssoc($element)) {
            $flatArray += $element;
        }
            foreach ($element as $key => $node) {
//            if (is_array($node))
                if (array_key_exists('child', $node)) {
                    $flatArray +=  $this->flatten($node['child'], $flatArray);
                    unset($node['child']);
                    $flatArray[] = $node;
//                    if (count($flat)>1)
//                    $flatArray[] = $flat;

                } else {
                    $flatArray[] = $node;
                }
            }


        return $flatArray;
    }
/*****  Not working one  *****
    function flatten($element)
    {
        $flatArray = array();
        if (count($element) == 1 && !array_key_exists('child', $element) && !is_array($element)) {
            $flatArray[] = $element;
        }
        foreach ($element as $key => $node) {
//            if (is_array($node))
            if (array_key_exists('child', $node)) {
                $flatArray =  array_merge($flatArray, $this->flatten($node['child']));
                unset($node['child']);
                $flatArray[] = $node;
//                    if (count($flat)>1)
//                    $flatArray[] = $flat;

            } else {
                $flatArray[] = $node;
            }
        }


        return $flatArray;
    }*/

    function typeValidator($level){
        if($level == 'Super admin')
            return ['Distributor', 'Dealer', 'Subdealer'];
        elseif ($level == 'Distributor')
            return ['Dealer', 'Subdealer'];
        elseif ($level == 'Dealer')
            return ['Subdealer'];
        else return array();

    }

    function UserCreation($newUser, $user){
        if (in_array($newUser['level'], $this->typeValidator($user->level))){
            if ($newUser['type'] == 'admin')
                return true;
        }
        elseif ($newUser['type'] != 'admin' && $newUser['level'] == $user->level)
            return true;

        return false;
    }


    function UserEdit($newUser, $user){
        if (in_array($newUser->level, $this->typeValidator($user->level))){
            if ($newUser->type == 'admin')
                return false;

        else
            return true;
        }
        elseif ($newUser->type != 'admin' && $newUser->level == $user->level)
            return true;
        return false;
    }


    protected function solveUsers($users)
    {

        foreach ($users as $key => $user) {

            $users[$key]['active'] = Order::employee($user['id'])->filter('active')->count();
            $users[$key]['pending'] = Order::employee($user['id'])->filter('pending')->count();
            $users[$key]['waiting'] = Order::employee($user['id'])->filter('waiting')->count();

        }
        return $users;

    }

    public function subNetID ($flatNetwork, $id = null)
    {
        if ($id == null)
            $user = Auth::user();
        else {

            $user = User::find($id);
//            dd($user);
        }
        $ids= array();
//
        if ($user->type != 'admin'){
            $flatNetwork = $this->getMyFlatNetwork($user->supervisor_id);
//
//            foreach ($friends as $friend){
//                $ids[] = $friend['id'];
//            }
//            $ids[] = $user->supervisor_id;
//
//
        }

        foreach ($flatNetwork as $item) {
            $ids[] = $item['id'];
        }
//        dd($ids);
        return $ids;
    }


    public function deleteUser($user)
    {
        DB::transaction(function () use ($user) {

        $parent = User::find($user->supervisor_id);
        $myCildren = User::where('supervisor_id', $user->id)->where('level', $user->level)->where('type', '!=', 'admin')->get();
        foreach ($myCildren as $item) {
            $this->changeOrderOwner($item, $parent);
            $item->delete();
        }
        $children = User::where('supervisor_id', $user->id)->get();
        foreach ($children as $child){
            $child->supervisor_id = $parent->id;
            $this->changeOrderOwner($child, $parent);
            $child->save();
        }


            $this->changeOrderOwner($user, $parent);

        $user->delete();
        }, 5);
    }

    protected function changeOrderOwner($fromUser, $toUser)
    {
        $orders = Order::withTrashed()->where('created_by', $fromUser->id)->get();
        foreach ($orders as $order) {
            $order->created_by = $toUser->id;
            $order->save();
            }
    }

}

