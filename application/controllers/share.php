<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class Share extends CI_Controller {
        
        public function __construct()
        {
            parent::__construct();
            $this->load->model('share_model');
            $this->load->model('share_comment_model');
        }
        public function index()
        {
            echo json_encode(array('Hello'=>'Weather'));
        }
        
        public function get_share()
        {
            // 如果什麼都沒有傳，就全部抓
            
            $where = array();
            
            // 如果有傳時間限制
            if(isset($_POST['share_time']))
            {
                $share_time = $this->input->post('share_time', TRUE);
                
                $where['share_time >='] = date('Y-m-d H:i:s', strtotime($share_time));
            }
            
            // 如果有傳區域限制
            if(isset($_POST['share_latitude_max']) && isset($_POST['share_latitude_min']) && isset($_POST['share_longitude_max']) && isset($_POST['share_longitude_min']))
            {
                
                $share_latitude_max = $this->input->post('share_latitude_max', TRUE);
                $share_latitude_min = $this->input->post('share_latitude_min', TRUE);
                $share_longitude_max = $this->input->post('share_longitude_max', TRUE);
                $share_longitude_min = $this->input->post('share_longitude_min', TRUE);
                
                $where['share_latitude <='] = $share_latitude_max;
                $where['share_latitude >='] = $share_latitude_min;
                $where['share_longitude <='] = $share_longitude_max;
                $where['share_longitude >='] = $share_longitude_min;
            }
            
            
            $query = $this->share_model->get_share($where);
            
            $shares = $query->result();
            
            // 這邊開始將每一篇的comment抓下來
            foreach($shares as $share)
            {
                $share_id = $share->share_id;
                $where_comment = array('share_id'=>$share_id);
                $query_comment = $this->share_comment_model->get_share_comment($where_comment);
                $share->share_comment = $query_comment->result();
            }
            
            // 將最後結果送出
            echo json_encode(array('constraints' => $where,
                                   'result' => $shares
                                   ));
        }
        
        public function insert_share()
        {
            
            
            // 防止沒有傳post value
            if(!isset($_POST['user_id']) OR !isset($_POST['share_content']) OR !isset($_POST['share_weather_type']) OR !isset($_POST['share_latitude']) OR !isset($_POST['share_longitude']))
            {
                echo json_encode(array('result'=>'wrong post value'));
                return;
            }
            
            $user_id = $this->input->post('user_id', TRUE);
            $share_content = $this->input->post('share_content', TRUE);
            $share_weather_type = $this->input->post('share_weather_type', TRUE);
            $share_latitude = $this->input->post('share_latitude', TRUE);
            $share_longitude = $this->input->post('share_longitude', TRUE);
            
            $data = array(
                          'user_id'=>$user_id,
                          'share_content'=>$share_content,
                          'share_weather_type'=>$share_weather_type,
                          'share_latitude'=>$share_latitude,
                          'share_longitude'=>$share_longitude,
                          'share_time'=>date("Y-m-d H:i:s"),
                          'share_likes'=>0
                          );
            
            $result = $this->share_model->insert_share($data);
            
            echo json_encode(array('result'=>$result));
        }
        
        public function delete_share()
        {
            // 刪除時必須提供user_id和share_id
            
            // 防止沒有傳post value
            if(!isset($_POST['user_id']) OR !isset($_POST['share_id']))
            {
                echo json_encode(array('result'=>'wrong post value'));
                return;
            }
            
            $user_id = $this->input->post('user_id', TRUE);
            $share_id = $this->input->post('share_id', TRUE);
            
            $data = array(
                          'user_id'=>$user_id,
                          'share_id'=>$share_id
                          );
            
            $result = $this->share_model->delete_share($data);
            
            echo json_encode(array('result'=>$result));

        }
        
        public function insert_share_comment()
        {
            // 防止沒有傳post value
            if(!isset($_POST['user_id']) OR !isset($_POST['share_id']) OR !isset($_POST['share_comment_content']))
            {
                echo json_encode(array('result'=>'wrong post value'));
                return;
            }
            
            $user_id = $this->input->post('user_id', TRUE);
            $share_id = $this->input->post('share_id', TRUE);
            $share_comment_content = $this->input->post('share_comment_content', TRUE);
            
            $data = array(
                          'user_id'=>$user_id,
                          'share_id'=>$share_id,
                          'share_comment_content'=>$share_comment_content,
                          'share_comment_time'=>date("Y-m-d H:i:s"),
                          );
            
            $result = $this->share_comment_model->insert_share_comment($data);
            
            echo json_encode(array('result'=>$result));
        }
        
        // 以下是參考用的function
        public function get_weather()
        {
            $where = array();
            $query = $this->question_model->get_question($where);
            $question_time = $query->row()->question_time;
            echo json_encode(array('Hello'=>'World','question_time'=>$question_time,'result' => $query->result()));
        }
        public function insert_weather()
        {
            $data = array('user_id'=>'2');
            $result = $this->question_model->insert_question($data);
            
            echo json_encode(array('result'=>$result));
        }
    }