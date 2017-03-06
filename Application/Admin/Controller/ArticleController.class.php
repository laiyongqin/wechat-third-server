<?php
/**
 * Created by PhpStorm.
 * Author: Lin07ux
 * Date: 2017-01-17
 * Time: 14:03
 * Desc:
 */

namespace Admin\Controller;


use Think\Upload;

class ArticleController extends CommonController
{
    /**
     * 获取文章列表
     */
    public function lists()
    {
        $page = I('get.page', 1);
        $rows = 7;

        $articles = D('Articles')->lists($page, $rows);

        if ($articles) {
            $res = ['code' => 0, 'msg' => '获取文章列表成功', 'data' => $articles];
        } else {
            $res = ['code' => 101, 'msg' => '获取文章列表失败，请稍后重试！'];
        }

        $this->ajaxReturn($res);
    }

    /**
     * 上传文章封面图片
     */
    public function upload()
    {
        $config = array(
            'maxSize'  => 2097152,
            'rootPath' => C('UPLOAD_PATH'),
            'savePath' => 'Articles/',
            'saveName' => array('uniqid',''),
            'autoSub'  => true,
            'subName'  => array('date','Ymd'),
            'exts'     => array('jpg', 'png', 'jpeg'),
        );
        $upload = new Upload($config);
        $info = $upload->uploadOne($_FILES['file']);

        if ($info) {
            $res = [
                'code' => 0,
                'msg' => '上传成功',
                'data' => '/'.C('UPLOAD_PATH')."{$info['savepath']}{$info['savename']}"
            ];
        } else {
            $err = $upload->getError();
            $res = ['code' => 150, 'msg' => $err ?: '上传失败，请稍后重试！',];
        }

        $this->ajaxReturn($res);
    }

    /**
     * 获取文章详细信息
     */
    public function detail()
    {
        $id = I('get.id');

        if (!$id || $id <= 0) {
            $res = ['code' => 10, 'msg' => '请提供正确的文章 ID'];
        } else {
            $detail = D('Articles')->detail($id);

            if ($detail) {
                $res = ['code' => 0, 'msg' => '获取文章信息成功', 'data' => $detail];
            } else {
                $res = ['code' => 101, 'msg' => '获取文章信息失败'];
            }
        }

        $this->ajaxReturn($res);
    }

    /**
     * 添加文章信息
     */
    public function add()
    {
        if (!IS_POST) $this->send404();

        $data = I('post.');
        if (isset($data['content'])) $data['content'] = $_POST['content'];

        $Article = D('Articles');
        $result = $Article->addArticle($data,  get_domain());

        if ($result) {
            $res = ['code' => 0, 'msg' => '添加文章成功', 'data' => $result];
        } else {
            $err = $Article->getError();
            $res = ['code' => 100, 'msg' => $err ?: '添加文章失败，请稍后重试。'];
        }

        $this->ajaxReturn($res);
    }

    /**
     * 编辑文章信息
     */
    public function edit()
    {
        if (!IS_POST) $this->send404();

        $data = I('post.');
        if (isset($data['content'])) $data['content'] = $_POST['content'];

        $Article = D('Articles');
        $result = $Article->editArticle($data);

        if ($result) {
            $res = ['code' => 0, 'msg' => '更新文章信息成功',];
        } else {
            $err = $Article->getError();
            $res = ['code' => 100, 'msg' => $err ?: '更新文章信息失败，请稍后重试。'];
        }

        $this->ajaxReturn($res);
    }

    /**
     * 删除文章
     */
    public function remove()
    {
        if (!IS_POST) $this->send404();

        $Article = D('Articles');
        $result = $Article->remove(I('post.id'));

        if ($result) {
            $res = ['code' => 0, 'msg' => '删除成功',];
        } else {
            $err = $Article->getError();
            $res = ['code' => 1501, 'msg' => $err ?: '删除失败，请稍后重试！'];
        }

        $this->ajaxReturn($res);
    }

    /**
     * 搜索文章
     */
    public function search()
    {
        $articles = D('Articles')->search(I('post.query'));

        if ($articles) {
            $res = ['code' => 0, 'msg' => '查询成功', 'data' => $articles];
        } else {
            $res = ['code' => 1502, 'msg' => '查询失败。请稍后重试。'];
        }

        $this->ajaxReturn($res);
    }

    /**
     * 获取图文消息的文章信息
     */
    public function news()
    {
        $news = D('Articles')->news(I('get.news'), session('user.wx_appid'));

        if (false !== $news) {
            if (is_null($news)) $news = [];
            $res = ['code' => 0, 'msg' => '获取图文消息内容成功', 'data' => $news];
        } else {
            $res = ['code' => 1502, 'msg' => '获取图文消息内容失败',];
        }

        $this->ajaxReturn($res);
    }
}