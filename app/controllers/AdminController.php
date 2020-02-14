<?php

use Phalcon\Session\Manager;
use Phalcon\Http\Request;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Growave2\Plugins\Acl;
use Growave2\Models\TimeTracker;


class AdminController extends Growave2\Controllers\ControllerBase
{
    public function initialize()
    {
        Phalcon\Tag::setTitle('Админ');
    }
    private function _redirect($controller,$method)
    {
        $this->dispatcher->forward(
            [
                'controller' => $controller,
                'action' => $method,
            ]
        );
    }

    public function indexAction()
    {

    }

    public function createAction()
    {
        $this->view->form = new Growave2\Forms\RegisterForm();
    }

    public function showAllUsersAction()
    {
        $this->view->users = Growave2\Models\Users::find();
    }

    public function particularUserAction($id = null)
    {
         if ($id == null)  $id = $this->request->getPost('hidden');
            $user = Growave2\Models\Users::findFirst(['id = :id:', 'bind' => ['id' => $id],])->toArray();
            $this->view->form = new Growave2\Forms\SelectDateForm();
            $userData = Growave2\Models\TimeTracker::find(['user_id = :id:', 'bind' => ['id' => $id],])->toArray();
            $user['times'] = $userData;
        if ($user)
        {
            if ($this->request->isPost())
            {
                $year = $this->request->getPost('year');
                $month = $this->request->getPost('month');
                /*Получаем кол-во дней в одном месяце*/
                $dateByYearMonth = $this->times->DateMonth($year, $month);
            }
            else {
                $date = $this->times->createDateOFToday();
                $year = $date->format('Y');
                $month = $date->format('m');
                $dateByYearMonth = $this->times->DateMonth($year, $month);
            }
            ($this->request->isPost()) ?
            $ym = $this->request->getPost('year')."-".date('m',$this->request->getPost('month'))
            : $ym = date('Y')."-".date('m');

            $this->view->user = $user;
            $date = new DateTime( 'D',new DateTimeZone('Asia/Bishkek'));
            $this->view->date = $date->format('d l');
            $this->view->daysInMonth = $dateByYearMonth;
            $this->view->timeForm = new Growave2\Forms\UserTimesForm();
            $this->view->assignedHours = TimeTracker::getTotalAssignedHours($id,$ym);
            $this->view->totalLateHours = TimeTracker::getTotalLateHours($id,$ym);
            $this->view->date = $this->times->createDateOFToday();
            $this->view->year = $year;
            $this->view->month = $month;
            $this->view->nameOFmonth = $this->times->convertNumberToMonthName($month);
            $this->view->hourInPercent =  TimeTracker::workedHoursInPercentage($this->auth->getId(),$ym);
            $this->view->holidays = Growave2\Models\Holiday::find();

        }
    }

    public function searchDataByDateAction()
    {
        if ($this->request->isPost())
        {
            $year = $this->request->getPost('year');
            $month = $this->request->getPost('month');
            $user_id = $this->request->getPost('hidden');
            /*Получаем кол-во дней в одном месяце*/
            $dateByYearMonth = $this->times->DateMonth($year, $month);
        }
        else {
            $date = $this->times->createDateOFToday();
            $year = $date->format('Y');
            $month = $date->format('m');
            $dateByYearMonth = $this->times->DateMonth($year, $month);
        }
        $this->view->daysInMonth = $dateByYearMonth;
        /* обьек data*/
        $this->view->date = $this->times->createDateOFToday();
        $this->view->year = $year;
        $this->view->month = $month;
        $this->view->nameOFmonth = $this->times->convertNumberToMonthName($month);
        $this->view->user = Growave2\Models\Users::findFirst([
            'conditions'=> 'id = ?1',
            'bind' => [
                1 => $user_id,
            ]
        ])->toArray();
        $this->_redirect('admin','particularUser');
    }


    public function updateTimeAction()
    {
        if ($this->request->isPost())
        {
            $time_id = $this->request->getPost('hiden');
            $timeTracker = Growave2\Models\TimeTracker::findFirst([
                'conditions'=> 'time_id = ?1',
                'bind' => [
                    1 => $time_id,
                ]
            ]);
            $timeTracker->start_time = $this->request->getPost('start_time');
            $timeTracker->stop_time = $this->request->getPost('stop_time');
            $timeTracker->total = $this->request->getPost('total');
            $timeTracker->late = $this->request->getPost('late');
            $timeTracker->assigned =  $this->request->getPost('assigned');

            if($timeTracker->update())
            {
                $this->flash->success('Успешно изменен');
                $this->_redirect('admin','showAllUsers');
            }
            else
             {
                $this->flash->error('Не удалость изменить');
                 $this->_redirect('admin','panel');
            }
        }
        else
            {
                $this->_redirect('admin','partic');
            }
    }


    public function panelAction()
    {

    }


    public function deleteUserAction()
    {
            $this->view->users = Growave2\Models\Users::find(['order'=>'first_name ASC']);
            $this->view->pick('admin/delete-user');
    }


    public function deleteAction($user_id)
    {

            $user = Growave2\Models\Users::findFirst([
                'conditions'=> 'id = ?1',
                'bind' => [
                    1 => $user_id,
                ]
            ]);
            if($user)
            {
                if($user->role_id !== "admin")
                {
                    $user->delete();
                    $this->flash->success('Пользователь удален!');
                    $this->_redirect('admin','deleteUser');
                }
                else
                {
                    $this->flash->error('У вас нет права доступа удаление!');
                    $this->_redirect('admin','deleteUser');
                }

            }
            else
            {
                $this->flash->error('Извините, нету такого пользователя');
                $this::_redirect('admin', 'deleteUser');
            }

    }


    public function editUserDataAction($user_id)
    {
            $user = Growave2\Models\Users::findFirst([
                'conditions'=> 'id = ?1',
                'bind' => [
                    1 => $user_id,
                ]
            ]);
            if($user->role_id != 'admin')
            {
                $this->view->form = new Growave2\Forms\RegisterForm();
                $this->view->user = $user;
            }
            else
            {
                $this->flash->warning('Невозможно изменить данные этого пользователя!');
                $this->_redirect('admin','deleteUser');
            }

    }



    public  function logoutAction ()
    {
        $this->auth->remove('auth');
        $this->_redirect('index','index');

    }


    public  function updateAction ()
    {
        $request = new Request;
        $form = new \Growave2\Forms\RegisterForm();
        if (!$this->request->isPost())
        {
            $this::_redirect('admin','editUserData');
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
        }
        $email = $request->getPost('email');
        $userD = Growave2\Models\Users::findFirst([
            'conditions'=> 'email = ?1',
            'bind' => [ 1 => $email,] ]);
        $userD->first_name = $request->getPost('first_name');
        $userD->email = $email;
        $userD->password = $this->security->hash( $request->getPost('password'));
        $userD->role_id = $request->getPost('role_id');
        $userD->active = $request->getPost('activeStatus');

        if(!$userD->update())
        {
            $this->flash->error('Не получилось изменить данные пользователя');
            $this->_redirect('admin','deleteUser');
        }
        else
        {
            $this->flash->success('Вы изменили данные пользователя!');
            $this->_redirect('admin','deleteUser');
        }
    }

    public function getHolidaysAction()
    {
        $this->view->holidays = Growave2\Models\Holiday::find();
    }

    public function manipulateHolidaysAction()
    {
        $this->view->holidays = Growave2\Models\Holiday::find();
        $this->view->checkboxForm = new \Growave2\Forms\CheckBoxForm();
        $this->view->selectDateForm = new \Growave2\Forms\SelectDateForm();
    }
    public function setHolidaysAction()
    {
        $request = $this->request;
        if ($request->isPost())
        {
                $date = new DateTime();
                $postYear = $this->request->getPost("year");
                $postMonth = $this->request->getPost("month");
                $postDay  = $this->request->getPost("day");
                $description  = $this->request->getPost("description");
                $dateFromPost =  date('Y-m-d', strtotime($date->format("$postYear-$postMonth-$postDay")));
                $checked = $this->request->getPost("checkBox");

                $month = $this->times->getMonthFromDate($dateFromPost);
                $yea = $this->times->getYearFromDate($dateFromPost);
                $day = $this->times->getDayFromDate($dateFromPost);
                $remainingNumber = $this->times->getRemainingNumberFromYear($dateFromPost);

                if ($checked)
                {
                    $date = new DateTime();
                    for ($i = $remainingNumber; $i < $remainingNumber+3; $i++)
                    {
                        $year = $yea.$i;
                        $holiday_date = date('Y-m-d', strtotime($date->format("$year-$month-$day")));
                        $holidays = new Growave2\Models\Holiday();
                        $holidays->holiday = $description;
                        $holidays->holiday_date = $holiday_date;
                        $holidays->checked = $checked;
                        $holidays->day = $postDay;
                    }
                    echo ($holidays->save()) ? "Запись на три года" : "Не удалось";
                    $this->_redirect('admin','manipulateHolidays');
                }
                else
                {
                    $holidays = new Growave2\Models\Holiday();
                    $holidays->holiday = $description;
                    $holidays->holiday_date = $dateFromPost;
                    $holidays->checked = "off";
                    $holidays->day = $postDay;
                    echo ($holidays->save()) ? "Запись на текущий год": "Не удалось";
                    $this->_redirect('admin','manipulateHolidays');
                }

        }
    }


}
