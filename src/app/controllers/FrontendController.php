<?php

use Phalcon\Mvc\Controller;

class FrontendController extends Controller
{
    public function indexAction()
    {
        // $this->view->users = Users::find();
        // echo "jhsvdjvjsvd";
    }

    public function homeAction()
    {
        $this->session->path = $_GET['_url'];
        $this->view->data = Products::find();
    }

    public function addToCartAction()
    {
        if ($this->session->cart == null) {
            $arr = [];
        } else {
            $arr = $this->session->cart;
        }

        $id = $this->request->getPost('addtocart');
        $qty = $this->request->getPost('quantity');
        $product =  Products::findFirst([
            'conditions' => 'id= :id:',
            'bind' => [
                'id' => $id,
            ]
        ]);
        $product = json_decode(json_encode($product));
        if (count($arr) == 0) {
            $toPush = ['product' => $product, 'qty' => $qty];
            array_push($arr, $toPush);
            $this->session->cart = $arr;
        } else {
            if ($this->arrayIncludesAction($id, $qty)) {
            } else {
                array_push($arr, ['product' => $product, 'qty' => $qty]);
                $this->session->cart = $arr;
            }
        }
        $this->response->redirect($this->session->path);
    }
    public function arrayIncludesAction($id, $qty)
    {
        $arr = $this->session->cart;
        foreach ($arr as $key => $value) {
            if ($id == $arr[$key]['product']->id) {
                $arr[$key]["qty"] = $qty;
                $this->session->cart = $arr;
                return true;
            }
        }
        $this->session->cart = $arr;
        return false;
    }
    public function cartAction()
    {
        if ($this->session->loginUser != null) {

            $this->session->path = $_GET['_url'];
        } else {
            $this->response->redirect('user/signin');
        }
    }

    public function checkoutAction()
    {
        if ($this->session->loginUser != null) {
            if ($this->request->isPost()) {
                $order = new Orders();

                foreach ($this->session->cart as $key => $value) {
                    // echo $value['product']->id;
                    // die();
                    $order = new Orders();
                    $order->product_id = $value['product']->id;
                    $order->product_name = $value['product']->name;
                    $order->quantity = $value['qty'];
                    $order->price = $value['product']->price;
                    $order->email = $this->session->loginUser->email;
                    $order->save();
                }

                $this->session->cart = [];
            } else {
                $this->response->redirect('user/signin');
            }
        }
    }
}
