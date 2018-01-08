<?php
/**
 * Created by PhpStorm.
 * User: lance
 * Date: 17-12-31
 * Time: 下午3:44
 */

/**
 * 管理员管理
 * Class Admins
 */
//class Admins extends REST_Controller
class Admins extends REST_Controller
{
    public function __construct(string $config = 'rest')
    {
        parent::__construct($config);
        //加载数据库
        $this->load->database();
        //加载表单验证类，以及jwt
        $this->load->library(["form_validation", "jwt"]);
        //加载管理员模型
        $this->load->model("Admins_model");
        //设置所有验证信息
        array_map(function ($a_item) {
            $this->form_validation->set_message($a_item[0], $a_item[1]);
        }, [
            ['min_length', '{field}长度不小于{$param}'],
            ['require', '{field}必填']
        ]);


    }

    //获取管理员
    public function index_get()
    {
        $this->getAdmins();
    }

    //新增管理员
    public function index_post()
    {
        $this->addAdmin();
    }

    //删除管理员
    public function index_delete()
    {
        $this->deleteAdmin();
    }

    /**
     * 管理员登录接口
     */
    public function signin_post()
    {
        //验证用户名和密码
        $this->form_validation->set_rules([
            [
                'field' => 'name',
                'label' => '用户名',
                'rules' => 'trim|required|min_length[1]',
            ],
            [
                'field' => 'password',
                'label' => '密码',
                'rules' => 'trim|required|min_length[1]',
            ]
        ]);
        //验证,参数
        if ($this->form_validation->run() === false):
            $this->response(['error' => $this->validation_errors()], 400);
        else:
            if ($a_user = $this->Admins_model->userIsExists($this->input->post('name'))):
                //判断密码是否正确
                if (md5($this->input->post('password')) !== $a_user['password']):
                    $this->response(['error' => "密码错误"], 401);
                endif;
                //判断是否管理员
                if (!$this->Admins_model->isAdmin($a_user['id'])):
                    $this->response(["error" => "身份非管理员"], 401);
                endif;
                //登录成功
                $s_jwt = $this->jwt->encode(
                    [
                        'user_id' => $a_user['id'],
                        'expire_time' => time() + 3600 * 3
                    ], $this->config->item('encryption_key')
                );
                $this->response([
                    'id' => $a_user['id'],
                    'name' => $a_user['name'],
                    'mail' => $a_user['mail'],
                    'Access-Token' => $s_jwt
                ], 200);
            else:
                //用户名不存在
                $this->response(['error' => "用户名不存在"], 401);
            endif;
        endif;
    }

    /**
     * 新增管理员
     */
    private function addAdmin()
    {
        //验证身份
        $a_payload = $this->validate();
        $this->form_validation->set_rules([
            [
                'field' => 'name',
                'label' => '管理员名',
                'rules' => 'trim|required|min_length[1]',
            ],
        ]);
        //验证,参数
        if ($this->form_validation->run() === false):
            $this->response(['error' => $this->validation_errors()], 400);
        else:
            //管理员名称存在
            if ($this->Admins_model->userIsExists($this->input->post('name'))):
                $this->response(['error' => "管理员名称已存在"], 400);
            endif;
            //todo 验证权限

            if ($this->auth('addAdmin', $a_payload->user_id)):
                if ($this->Admins_model->addAdmin([
                    'name' => $this->input->post('name'),
                    'password' => md5('123456'),
                    'mail' => '',
                    'create_at' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
                ])):
                    $this->response(['success' => '添加成功'], 201);
                else:
                    $this->response(['error' => "添加失败"], 400);
                endif;
            else:
                $this->response(['error' => "没有操作权限"], 403);
            endif;
        endif;
    }

    /**
     * 删除管理员
     */
    public function deleteAdmin()
    {
        $a_payload = $this->validate();
        $i_id = intval(file_get_contents("php://input"));
        if (empty($i_id)):
            $this->response(['error' => '参数错误'], 400);
        else:
            //todo 验证权限
            if ($this->auth('deleteAdmin', $a_payload->user_id)):
                //管理员不存在
                if (!$this->Admins_model->userIsById($i_id)):
                    $this->response(['error' => '要删除的管理员不存在'], 400);
                endif;
                if ($this->Admins_model->deleteAdmin($i_id)):
                    $this->response(['success' => '已删除管理员'], 200);
                else:
                    $this->response(['error' => '删除失败'], 400);
                endif;
            else:
                $this->response(['error' => "没有操作权限"], 403);
            endif;
        endif;
    }

    /**
     * 获取管理员
     */
    public function getAdmins()
    {
        $a_payload = $this->validate();
        //todo 验证权限
        if ($this->auth('getAdmins', $a_payload->user_id)):
            //获取的字段
            $a_fields = ['id', 'name'];
            $a_admins = $this->db
                ->select($a_fields)
                ->get_where('users_auth')
                ->result_array();
            return $this->response($a_admins, 200);
        else:
            $this->response(['error' => "没有操作权限"], 403);
        endif;
    }

    //验证请求者的身份
    private function validate()
    {
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        try {
            $token = $this->jwt->decode($jwt, $this->config->item('encryption_key'));
            if (time() > $token->expire_time):
                return $this->response(['error' => '身份信息已过期，请重新获取'], 401);
            endif;
            return $token;
        } catch (InvalidArgumentException $e) {
            return $this->response(['error' => '身份信息已失效，请重新获取'], 401);
        } catch (UnexpectedValueException $e) {
            return $this->response(['error' => '身份信息已失效，请重新获取'], 401);
        } catch (DomainException $e) {
            return $this->response(['error' => '身份信息已失效，请重新获取'], 401);
        }
    }

    /**
     * 判断用户是否具有某个权限
     * @param string $s_identity 权限标识
     * @param int $i_id 用户 id
     * @return bool
     */
    private function auth(string $s_identity, int $i_id)
    {
        //获取用户权限码
        $i_auth = $this->db->select('auth')->get_where('users_auth', ['id' => $i_id], 1)->result_array()[0]['auth'] ?? false;
        //判断是否为最高管理员,偏移为1
        if ($i_auth & (1 << 1)):
            return true;
        endif;

        //获取对应权限偏移
        $i_authOffset = $this->db->select('id')->get_where('auth_detail', ['indentity' => $s_identity], 1)
                ->result_array()[0]['id'] ?? false;
        if ($i_auth && $i_authOffset):
            //比较权限
            return ($i_auth & (1 << $i_authOffset)) && true;
        else:
            return false;
        endif;
    }
}