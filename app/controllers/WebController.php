<?php


namespace app\controllers;

use app\services\DashboardService;
use app\services\GiveHelpService;
use app\services\ReferenceService;
use app\services\NotificationService;
use app\services\UserService;
use app\controllers\GiveHelpController;
use app\services\AuthService;
use app\services\MerchantService;
use app\controllers\LevelController;
use app\services\PaymentService;

class WebController
{

    public function home()
    {

        echo tView("website/home");
    }

    public function service()
    {
        echo tView("website/services");
    }


    public function contact()
    {
        echo tView("website/contact");
    }

    public function about()
    {
        echo tView("website/about");
    }

    public  function privacy()
    {

        echo tView("website/pp");
    }

    public function terms()
    {
        echo tView("website/tc");
    }

    public function refund()
    {
        echo tView("website/rc");
    }
    public function partners()
    {

        $merchantService = new MerchantService();
        $data = $merchantService->getMerchantsLists();
        $res = json_decode($data, true);
        echo tView("website/partners", array('res' => $res));
    }

    public function getUserId()
    {
        $Oauth = new AuthService;
        $userId = $Oauth->validate();
        return $userId;
    }

    public function signUpView()
    {
        $connectId = '';
        if (isset($_REQUEST['ReferenceID']) && !empty($_REQUEST['ReferenceID'])) {
            $connectId = $_REQUEST['ReferenceID'];
        }
        echo tView("dashboard/registration", array('connectId' => $connectId));
    }

    public function payment()
    {
        echo tView("dashboard/payment");
    }
    public function loginView()
    {
        echo tView("login");
    }

    public function homeView()
    {
        $_SESSION['Authorization'] =  'Bearer ' . $_COOKIE['accessToken'];
        $pmfStatus = self::checkInitialPSC();
        $dashboardService = new DashboardService();
        $data = $dashboardService->DashboardAction();
        $res = json_decode($data, true);
        echo tView("home", array('res' => $res, 'pmfStatus' => $pmfStatus));
    }

    public function giveHelp()
    {
        $pmfStatus = self::checkInitialPSC();
        $giveHelpService = new GiveHelpService();
        $data = $giveHelpService->getGiveHelpList();
        $res = json_decode($data, true);

        echo tView("giveHelp", array('res' => $res, 'pmfStatus' => $pmfStatus));
    }
    public function myConnects()
    {
        $pmfStatus = self::checkInitialPSC();
        $referenceService = new ReferenceService();
        $data = $referenceService->getMyReference();
        $res = json_decode($data, true);
        echo tView("myConnects", array('res' => $res, 'pmfStatus' => $pmfStatus));
    }

    public function notification()
    {
        $pmfStatus = self::checkInitialPSC();
        $notificationService = new NotificationService();
        $data = $notificationService->getMyNotification();
        $res = json_decode($data, true);
        echo tView("notification", array('res' => $res, 'pmfStatus' => $pmfStatus));
    }
    public function psc()
    {
        $pmfStatus = self::checkInitialPSC();
        $dashboardService = new DashboardService();
        $data = $dashboardService->DashboardAction();
        $res = json_decode($data, true);
        $count = 0;
        $count = ($res['data'][0]['totalPSC'] + $res['data'][0]['totalTax']) / 500;
        echo tView("psc", array('totalPMF' => $count, 'pmfStatus' => $pmfStatus));
    }
    public function merchants()
    {
        $pmfStatus = self::checkInitialPSC();
        $merchantService = new MerchantService();
        $data = $merchantService->getMerchantsLists();
        $res = json_decode($data, true);

        echo tView("merchants", array('res' => $res, 'pmfStatus' => $pmfStatus));
    }

    public function giveHelpUserDetails($data)
    {
        $userId = $data['id'];
        $pmfStatus = self::checkInitialPSC();
        $giveHelpController = new GiveHelpController();
        $data = $giveHelpController->getUserData($userId);
        $res = json_decode($data, true);
        echo tView("giveHelpUserDetails", array('res' => $res, 'userId' => $userId, 'pmfStatus' => $pmfStatus));
    }
    public function receiveHelp()
    {
        $pmfStatus = self::checkInitialPSC();
        $giveHelpController = new GiveHelpController();
        $level = $giveHelpController->getUserLevel();
        $currentStage = $level - 1;
        echo tView("receiveHelp", array('stage' => $currentStage, 'pmfStatus' => $pmfStatus));
    }

    public function privilegeCard()
    {
        $pmfStatus = self::checkInitialPSC();
        $userService = new UserService();
        $data = $userService->getUserData();
        $res = json_decode($data, true);
        echo tView("privilegeCard", array('res' => $res, 'pmfStatus' => $pmfStatus));
    }
    public function password()
    {
        $pmfStatus = self::checkInitialPSC();
        $giveHelpService = new GiveHelpService();
        $data = $giveHelpService->getGiveHelpList();
        $res = json_decode($data, true);
        $status = false;
        foreach ($res['data'] as $value) {
            if ($value['status'] == 'Completed' || $value['status'] == 'Waiting for conformation') {
                $status = true;
            }
        }
        echo tView("password", array('status' => $status, 'userId' => self::getUserId(), 'pmfStatus' => $pmfStatus));
    }

    public function logout()
    {
        session_destroy();
        unset($_SESSION["Authorization"]);
        unset($_COOKIE['accessToken']);
        setcookie('accessToken', null, -1, '/');
        echo tView("login");
    }
    public function userProfile()
    {
        $pmfStatus = self::checkInitialPSC();
        $userService = new UserService();
        $data = $userService->getUserData();
        $res = json_decode($data, true);

        echo tView("profile", array('res' => $res, 'host' => $_SERVER['HTTP_HOST'], 'pmfStatus' => $pmfStatus));
    }

    public function levels()
    {
        $pmfStatus = self::checkInitialPSC();
        $levelController = new LevelController();
        $data = $levelController->getLevelDetails($_COOKIE['level']);
        $res = json_decode($data, true);
        echo tView("levels", array('res' => $res['data'], 'pmfStatus' => $pmfStatus));
    }

    public function forgotPassword()
    {
        echo tView("forgotPassword");
    }

    public function checkInitialPSC()
    {
        $userId = self::getUserId();
        $paymentService = new PaymentService();
        $data = $paymentService->checkInitialPSC($userId);
        $res = json_decode($data, true);
        return $res;
    }

    public function paymentHistory()
    {
        $res = self::payHistory();
        $pmfStatus = self::checkInitialPSC();
        echo tView("paymentHistory", array('res' => $res, 'pmfStatus' => $pmfStatus));
    }
    public function payHistory()
    {
        $paymentService = new PaymentService();
        $data = $paymentService->getPaymentHistory();
        $res = json_decode($data, true);
        return $res;
    }

    public function viewPayHistory($historyId)
    {
        $res = self::payHistory();
        $pmfStatus = self::checkInitialPSC();
        for ($i = 0; $i < count($res['data']); $i++) {
            if ($res['data'][$i]['id'] == $historyId['id']) {
                $data = $res['data'][$i];

                $giveHelpController = new GiveHelpController();
                $userData = $giveHelpController->getUserData($res['data'][$i]['user_id']);
                $userDetails = json_decode($userData, true);
            }
        }
        echo tView("viewpaymentHistory", array('res' => $data, 'user' => $userDetails, 'pmfStatus' => $pmfStatus));
    }
}
