<?php 
    class SqlsrvHelper
    {
        private $conn;
        private $serverName="localhost";
        private $uid="sa";
        private $pwd="lx128SIMON";
        private $database="ReportServer";
        private $connectionInfo;
        
        
        public function __construct()
        {
            $this->connectionInfo=array("UID"=>$this->uid,"PWD"=>$this->pwd,"Database"=>$this->database);
            $this->conn=sqlsrv_connect($this->serverName,$this->connectionInfo);
            if($this->conn == false)
            {
                echo "Unable to connect.</br>";  
		        die( print_r( sqlsrv_errors(), true));  
            }
            sqlsrv_query($this->conn, "set names utf8");
        }
        
        public function executeDqlArray($sql)//执行查询操作，返回结果集
        {
            $arr=array();
            $res=sqlsrv_query($this->conn, $sql) or die(print_r(sqlsrv_errors(),true));
            $i=0;
            while ($row=sqlsrv_fetch_array($res))
            {
                $arr[$i++]=$row;
            }
            sqlsrv_free_stmt($res);
            return $arr;
        }
        
        public function executeDml($sql)
        {
            $b=sqlsrv_query($this->conn, $sql);
            if(!$b)
            {
                echo "操作失败";
                die(print_r(sqlsrv_errors(),true));
                return 0;
            }
            else
            {
                if(sqlsrv_rows_affected($b)>0)
                {
                    return 1;
                    echo "操作成功";
                }
                else
                {
                    return 2;
                    echo "没有影响到行数";
                }  
            }
        }
        
        public function closeConnection()
        {
            if(!empty($this->conn))
            {
                sqlsrv_close($this->conn);
            }
        }
        
        public function executeDqlFenYe($sql1,$sql2,$fenYePage)//引用传递，利用这个函数，把FenYePage中的成员变量的值利用实例$fenYePage得到，
        {
            $arr=array();
            $res=sqlsrv_query($this->conn, $sql1) or die(print_r(sqlsrv_errors(),true));
            $i=0;
            while ($row=sqlsrv_fetch_array($res))
            {
                $arr[$i++]=$row;
            }
            sqlsrv_free_stmt($res);
            $fenYePage->resArray=$arr;
             
            $res2=sqlsrv_query($this->conn, $sql2) or die(print_r(sqlsrv_errors(),true));
            if($row=sqlsrv_fetch_array($res))
            {
                $fenYePage->rowCount=$row[0];
                $fenYePage->pageCount=ceil($row[0]/$fenYePage->pageSize);
            }
            sqlsrv_free_stmt($res2);
             
             
             
            //第三种分页方式，上一页，下一页的分页方式
            $navigate="";
            $navigate="<a href={$fenYePage->gotoUrl}?pageNow=1>首页</a>&nbsp";
            if($fenYePage->pageNow>1)
            {
                $pagePre=$fenYePage->pageNow-1;
                $navigate.="<a href={$fenYePage->gotoUrl}?pageNow=".$pagePre.">上一页</a>&nbsp";
            }
            if($fenYePage->pageNow<$fenYePage->pageCount)
            {
                $pageNex=$fenYePage->pageNow+1;
                $navigate.="<a href={$fenYePage->gotoUrl}?pageNow=".$pageNex.">下一页</a>&nbsp";
            }
            $navigate.="<a href={$fenYePage->gotoUrl}?pageNow=".$fenYePage->pageCount.">尾页</a>&nbsp";
            $navigate.="当前{$fenYePage->pageNow}页/共有{$fenYePage->pageCount}页";
            $fenYePage->navigate=$navigate;
        }
   
    }
?>