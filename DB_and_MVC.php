<?php

class db_information
{
    #DB 정보 저장
    const HOST = "localhost";
    const USER = "root";
    const PASSWORD = "autoset";
    const PORT = "3306";
    const DB_NAME = "midtermexam";
    const TABLE_NAME = "userinfo";
    private $connect;

    #객체 생성과 동시에 DB연결
    function __construct()
    {
        $this->connect = mysqli_connect(self::HOST, self::USER, self::PASSWORD,self::DB_NAME , self::PORT);
    }

    #메인으로 돌아가기 버튼
    function backmain()
    {
        echo "<form action='main.html'>";
        echo "<input type='submit' value='main돌아가기'>";
        echo "</form>";
    }


    #회원 정보 등록 function
    function registerationProcess()
    {
        $userid = $_GET['userid'];
        $classification = $_GET['classification'];
        $name = $_GET['username'];
        $gender = $_GET['gender'];
        $password = $_GET['userpassword'];
        $phone = $_GET['phone'];
        $email = $_GET['email'];

        $array = array('아이디' => $userid,'이름' => $name,'비밀번호' => $password,'핸드폰번호' => $phone,'이메일' => $email);
        $empty_array = array();
        $check_num = 0;

        foreach ($array as $key => $value) {
            if ($value == null) {
                $check_num++;
                array_push($empty_array, $key);
            }
        }

        if ($check_num == 0) {
            $query = "insert into " . self::TABLE_NAME . " values ('', '$userid', '$classification', 
        '$name', '$gender', '$password','$phone' , '$email')";

            $result = mysqli_query($this->connect, $query);

            if (isset($result)) {
                echo "디비 정상 입력완료";
            }
        }

        elseif ($check_num >= 1) {
            echo "<script>alert('입력되지 않은 값이 있습니다')</script>";
            foreach ($empty_array as $value){
                echo "$value<br>";

            }
            echo "---------<BR>";
        }
    }

    #회원 정보 수정 function => Ajax 필요
    function modifyProcess()
    {
        $check_user = $_GET['check_user'];

        $query = "select userid from " .self::TABLE_NAME. " where userid= '$check_user'";
        $result = mysqli_query($this->connect,$query);

        $row = mysqli_fetch_array($result);

        if($row['userid'] != null){

            $query = "select * from ".self::TABLE_NAME." where userid = '$check_user'";
            $result = mysqli_query($this->connect,$query);
            $arr = mysqli_fetch_array($result);

            if(!empty($arr)){
                echo json_encode($arr);
            }
        }

        else{
            $userid = $_GET['userid'];
            $classification = $_GET['classification'];
            $name = $_GET['username'];
            $gender = $_GET['gender'];
            $password = $_GET['userpassword'];
            $phone = $_GET['phone'];
            $email = $_GET['email'];

            $query = "update ". self::TABLE_NAME ." set userid='$userid', name='$name', classification='$classification',
             gender='$gender', phone='$phone', email='$email', password='$password' where userid='$check_user'";

            $result = mysqli_query($this->connect, $query);

            if(isset($result)){
                echo "정상입력 완료";
                $this->backmain();
            }
            else{
                echo "디비 변경 실패";
            }
        }
    }

    #회원 정보 삭제(로그인 유효성 검사 필요)
    function deleteProcess()
    {
        $id = $_GET['userid'];
        $password = $_GET['userpassword'];

        //---아이디, 비밀번호 유효성 검사필요
        $query = "select userid, password from " . self::TABLE_NAME;

        $result = mysqli_query($this->connect, $query)or die($this->connect);

        while ($print = mysqli_fetch_array($result)) {

            if ($print['userid'] == $id && $print['password'] == $password) {
                $query = "delete from " . self::TABLE_NAME . " where userid= '$id'";
                $result = mysqli_query($this->connect, $query);

                if (isset($result)) {
                    echo "<script>alert('삭제되었습니다')</script>";
                }
            } else if ($print['password'] == $password && $print['userid'] != $id) {
                echo "<script>alert('등록되지 않은 ID입니다')</script>";
            } else if ($print['userid'] == $id && $print['password'] != $password) {
                echo "<script>alert('잘못된 암호 입니다')</script>";
            }
        }

        $this->backmain();
    }

    function user_list()
    {
        echo "<table border='1'>";

        echo "<br>";
        echo "<tr>";
        echo "<td width='100'>sysid</td>";
        echo "<td width='100'>userid</td>";
        echo "<td width='100'>class</td>";
        echo "<td width='100'>name</td>";
        echo "<td width='100'>gender</td>";
        echo "<td width='100'>password</td>";
        echo "<td width='100'>phone</td>";
        echo "<td width='100'>email</td>";
        echo "<tr>";

        echo "</table>";
        echo "<br>";

        $query = "select * from " . self::TABLE_NAME;

        $result = mysqli_query($this->connect, $query);

        while ($print = mysqli_fetch_array($result)) {

            echo "<table style='text-align: center'>";
            echo "<tr>";
            echo "<td width='100'>" . $print['sysid'] . "</td>";
            echo "<td width='100'>" . $print['userid'] . "</td>";
            echo "<td width='100'>" . $print['classification'] . "</td>";
            echo "<td width='100'>" . $print['name'] . "</td>";
            echo "<td width='100'>" . $print['gender'] . "</td>";
            echo "<td width='100'>" . $print['password'] . "</td>";
            echo "<td width='100'>" . $print['phone'] . "</td>";
            echo "<td width='100'>" . $print['email'] . "</td>";
            echo "<tr>";
        }
        echo "</table>";
        $this->backmain();
    }
} //--- End Of db_information();


#DB 객체 생성 => 목적에 맞는 function으로 이동
$choice_function = new db_information();
$function = $_GET['function'];

switch ($function) {
    case "register":
        $choice_function->registerationProcess();
        break;

    case "modify":
        $choice_function->modifyProcess();
        echo "나는 : ".$function;
        break;

    case "delete":
        $choice_function->deleteProcess();
        break;

    case "list":
        $choice_function->user_list();

    default:
        break;
}
?>