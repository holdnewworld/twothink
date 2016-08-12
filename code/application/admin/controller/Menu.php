<?php
namespace app\admin\controller;
use think\Controller;
use think\db;
use think\response\View;
use think\Request;

class Menu extends Admin
{
    public function index()
    {
        $menuModel = new \app\admin\model\Menu();
        $id = Request::instance()->param('id');
        $pid = 0;
        $data = [];
        $pidval = [];

        if (!empty($id)) {
            $mainMenus = $menuModel->where(['pid' => $id])->order('id')->select();
            $pidval = $menuModel ->where(['id'=>$id])->find();
        } else {
            $mainMenus = $menuModel->where(['pid' => 0])->order('id')->select();
            $pidval['id'] = $pid;
            $pidval['title'] = '顶级菜单';
        }
        foreach ($mainMenus as $value) {
            $data[] = $value->toArray();
        }
        foreach ($data as &$val)
        {
            $val['parentTitle'] = $menuModel -> where(['id'=>$val['pid']])->value('title');
        }


        $this->view->assign('pidval',$pidval);
        $this->view->assign('data', $data);
        return $this->view->fetch();
    }

    public function add()
    {
        $pid = Request::instance()->param('pid');
        $menuModel = new \app\admin\model\Menu();
        if (Request::instance()->isPost()) {
            $data = Request::instance()->post(); // 获取经过过滤的全部post变量

            $add = Db::name('menu')->insert($data);
            if ($add) {
                $this->success('数据提交成功','/Admin/Menu/index');
            } else {
                $this->error('提交失败');
            }
        } else {
            $list[0]['id'] = 0;
            $list[0]['title'] = '顶级菜单';
            $arr = Db::table('geek_menu')->field('id,title')->select();
            $list = array_merge($list,$arr);

            $this->view->assign('list', $list);
            $this->view->assign('pid',$pid);
            return $this->view->fetch();
        }
    }

    public function edit($id)
    {
        $menuModel = new \app\admin\model\Menu();
        if (Request::instance()->isPost())
        {
            $postid = $_POST['id'];
            $save = $menuModel->allowField(true)->save($_POST,['id' => $postid]);
            if ($save)
            {
                $this -> success('数据更新成功','/Admin/Menu/index');
            }
            else
            {
                $this -> error('数据更新失败');
            }
        }
        else
        {
            $update = $menuModel->where(['id' => $id])->find();
            $data = $update->toArray();
            $parentMenu = $menuModel -> where(['id'=>$id])->value('pid');
            $list[0]['id'] = 0;
            $list[0]['title'] = '顶级菜单';
            $arr = Db::table('geek_menu')->field('id,title')->select();
            $list = array_merge($list,$arr);

            $this->view->assign('list', $list);
            $this->view->assign('data', $data);
            $this->view->assign('menupid',$parentMenu);
            return $this->view->fetch();
        }
    }

    public function delete($id)
    {

        $menuModel = new \app\admin\model\Menu();
        $delete = $menuModel -> destroy($id);

        if ($delete)
        {
            $this->redirect('/Admin/Menu/index');
        }
        else
        {
            $this->error('删除失败');
        }
    }
}
