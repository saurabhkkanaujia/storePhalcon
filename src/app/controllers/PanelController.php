<?php

use Phalcon\Mvc\Controller;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class PanelController extends Controller
{
    public function indexAction()
    {
    }

    public function dashboardAction()
    {
        if ($this->session->loginUser != null) {
            $this->view->userData = Users::find(
                [
                    'order' => 'id DESC',
                    'limit' => 5,
                ]
            );
            $this->view->productData = Products::find(
                [
                    'order' => 'id DESC',
                    'limit' => 5,
                ]
            );
        } else {
            $this->response->redirect('user/signin?err="Please Signin First"');
        }
    }

    public function allUsersAction()
    {
        // $this->view->data =  Users::find();
        if ($this->session->loginUser != null && $this->session->loginUser->role == 'admin') {
            $toSearch = $this->request->getPost();
            if ($this->session->loginUser->role == 'admin') {
                $currentPage = $this->request->getQuery('page', 'int', 1);
                $paginator   = new PaginatorModel(
                    [
                        'model'  => Users::class,
                        "parameters" => [
                            "id LIKE '%" . $toSearch['searchField'] . "%' OR name LIKE '%" . $toSearch['searchField'] . "%' OR username LIKE '%" . $toSearch['searchField'] . "%' OR email LIKE '%" . $toSearch['searchField'] . "%'",

                            "order" => "id"
                        ],
                        'limit' => 5,
                        'page'  => $currentPage,
                    ]
                );

                $page = $paginator->paginate();

                $this->view->setVar('page', $page);
            } else {
                // $data = ['message' => "Action Prohibited!. Please Signin as admin.."];
                $this->response->redirect('user/signin?err=Action Prohibited!. Please Signin as admin..');
            }
        } else {
            $this->response->redirect('user/signin?err=Please Signin First');
        }
    }

    public function alterUserAction()
    {

        $postData = $this->request->getPost();
        if (isset($postData['approve'])) {
            $id = $postData['approve'];
            $user = Users::findFirst($id);
            $user->status = "Approved";
        } elseif (isset($postData['restrict'])) {
            $id = $postData['restrict'];
            $user = Users::findFirst($id);
            $user->status = "Not Approved";
        } elseif (isset($postData['deleteUser'])) {
            $id = $postData['deleteUser'];
            $user = Users::findFirst($id);
            $user->delete();
        }
        $user->update();

        $this->response->redirect('panel/allUsers');
        // $this->dispatcher->forward(
        //     [
        //         'controller' => 'admin',
        //         'action' => 'allUsers',
        //     ]
        // );
    }
    public function allProductsAction()
    {
        if ($this->session->loginUser->role == 'admin') {
            $this->view->data =  Products::find();
        } else {
            $this->response->redirect('user/signin');
        }
    }

    public function addProductAction()
    {
        if ($this->request->isPost()) {
            $product = new Products();

            $product->assign(
                $this->request->getPost(),
                [
                    'name',
                    'category',
                    'description',
                    'price'
                ]
            );
            $success = $product->save();

            $this->view->success = $success;

            if ($success) {
                $this->response->redirect('panel/allProducts');
            } else {
                $this->view->message = "Product Not Added succesfully due to following reason: <br>" . implode("<br>", $product->getMessages());
            }
        }
    }

    public function alterProductAction()
    {
        $postData = $this->request->getPost();
        if (isset($postData['deleteProd'])) {
            $id = $postData['deleteProd'];
            $prod = Products::findFirst($id);
            $prod->delete();
            $this->response->redirect('/panel/allproducts');
        } elseif (isset($postData['updateProduct'])) {
            $id = $postData['updateProduct'];
            $prod = Products::findFirst($id);
            $prod->name = $postData['name'];
            $prod->category = $postData['category'];
            $prod->description = $postData['description'];
            $prod->price = $postData['price'];
            $prod->update();
            $this->response->redirect('/panel/allproducts');
        }

        $this->response->redirect('/panel/allProducts');
    }
    public function editProductAction()
    {
        $id = $_GET['id'];
        $product = Products::findFirst($id);
        $this->view->data = $product;
    }

    public function removeProductAction()
    {
        $id = $this->request->getPost('rmProduct');
        $arr = $this->session->cart;
        foreach ($this->session->cart as $key => $value) {
            if ($id == $arr[$key]['product']->id) {
                array_splice($arr, $key, 1);
            }
        }
        $this->session->cart = $arr;
        $this->response->redirect('/frontend/cart');
    }

    public function ordersAction()
    {
        if ($this->session->loginUser->role == 'admin') {
            $this->view->data =  Orders::find();
        } elseif ($this->session->loginUser->role == 'User') {
            $this->view->data = Orders::find([
                'conditions' => 'email= :email:',
                'bind' => [
                    'email' => $this->session->loginUser->email,
                ]
            ]);
        } else {
            $this->response->redirect('user/signin');
        }
    }
}
