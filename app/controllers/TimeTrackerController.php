<?php
use Phalcon\Mvc\Model\Manager as ModelsManager;
use \Growave2\Models\TimeTracker;
use \Growave2\Models\Users;


class TimeTrackerController extends \Growave2\Controllers\ControllerBase
{

    public function initialize()
    {
        Phalcon\Tag::setTitle('team');
    }
    protected function sendData($user_id)
    {
        $user = Users::findFirst([
            'conditions'=> 'id = ?1',
            'bind' => [
                1 => $user_id,
            ]
        ]);
        $this->view->today = $this->times->getDateOfToday();
        $this->view->user = $user->toArray();
    }

    private function _redirect($controller, $method)
    {
        $this->dispatcher->forward(
            [
                'controller' => $controller,
                'action' => $method,
            ]
        );

    }


    public function indexAction ()
    {
        $this->view->users = Growave2\Models\Users::find();
    }


    public  function teamAction ()
    {
        $this->view->selectForm = new Growave2\Forms\SelectDateForm();
        $this->view->current_user = $this->auth->getIdentity();
        $this->view->users = Growave2\Models\Users::find()->toArray();
        $this->view->trackedTimes =  Growave2\Models\TimeTracker::find();

        ($this->request->isPost()) ?
        $ym = $this->request->getPost('year')."-".date('m',$this->request->getPost('month'))
        : $ym = date('Y')."-".date('m');

        $this->view->assignedHours = TimeTracker::getTotalAssignedHours($this->auth->getId(),$ym);
        $this->view->totalLateHours = TimeTracker::getTotalLateHours($this->auth->getId(),$ym);
        $this->view->hourInPercent =  TimeTracker::workedHoursInPercentage($this->auth->getId(),$ym);
    }


    public function startTrackingAction()
    {
            $timeIs = $this->times->createTime();
            $this->session->set('start_time',$timeIs);
            $timeTracker = new TimeTracker();
            $timeTracker->user_id = $this->auth->getId();
            $timeTracker->date_tracker = date('Y:m:d');
            $timeTracker->start_time = $timeIs;
            $timeTracker->stop_time =  date('H:i:s',mktime(0,0,0));
            $timeTracker->total = date('H:i:s',mktime(0,0,0));
            $timeTracker->late =$this->times->getLateHours($timeIs);
            $timeTracker->assigned = 0;
            $timeTracker->ym = $this->times->getOnlyYearFromDate(date('Y-m-d'))."-".$this->times->getMonthFromDate(date('Y-m-d'));
            if ($timeTracker->save())
            {
                $this->flash->message('message', 'Хорошего рабочего дня. Не забудьте нажать на кнопку стоп!');
                $this->view->start_time = $timeIs;
                $this::sendData($this->auth->getId());
                $this->_redirect('time-tracker','team');
            }
    }

    public function stopTrackingAction()
    {
       $user_id = $this->auth->getId();
       $currentUserTime = TimeTracker::find([
            'conditions'=> 'user_id = ?1',
            'order' => 'time_id DESC',
            'bind' => [
                1 => $user_id,
            ]
        ])->toArray();

        $timeIs = $this->times->createTime();
        $total= $this->times->interval($this->session->get('start_time'), $timeIs);
        $timeTracker = TimeTracker::findFirst([
            'conditions'=> 'user_id = ?1 and start_time = ?2',
            'bind' => [
                1 => $user_id,
                2 => $currentUserTime[0]['start_time'] ]
        ]);
        $timeTracker->stop_time = $timeIs;
        $timeTracker->total = $total;
        $timeTracker->assigned = floor($this->times->assignedHoursPerDay($total));

        if ($timeTracker->update())
        {
            $this->flash->success('Всего доброго');
            $this::sendData($user_id);
            $this->view->start_time = $this->session->get('start_time');
            $this->view->stop_time = $timeIs;
            $this->view->totalHoursPerDay =$total;
            $this::_redirect('time-tracker','team');
        }
        else
            {
                echo "Не удалось записать в базу ваше время";
                $this::_redirect('time-tracker','index');
            }
        $this->_redirect('time-tracker','team');
    }


    public function showAction()
    {
        $users_data = Users::find(['order'=>'first_name ASC'])->toArray();
        $trackedTimes= TimeTracker::find()->toArray();

        for ($j =0; $j < count($users_data); $j++ )
        {
            for ( $i = 0; $i <  count($trackedTimes); $i++)
            {
                {
                    if ($users_data[$j]['id'] == $trackedTimes[$i]['user_id'])
                    {
                        $users_data[$j]['times'][] = $trackedTimes[$i];
                    }

                }
            }
        }
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

        $user = Users::findFirst([
            'conditions'=> 'id = ?1',
            'bind' => [
                1 => $this->auth->getId(),
            ]
        ])->toArray();

        $currentUserTimes = TimeTracker::find([
                'conditions'=> 'user_id = ?1',
                'bind' => [
                    1 => $this->auth->getId(),
                ]
            ])->toArray();
        foreach ($currentUserTimes as $time)
        {
            $user['times'][] = $time;
        }
        $this->view->users_d = $users_data;
        $this->view->holidays = Growave2\Models\Holiday::find();

        /*кол-во дней в одном  месяце*/
        $this->view->daysInMonth = $dateByYearMonth;

        /* обьект data*/
        $this->view->date = $this->times->createDateOFToday();

        $this->view->year = $year;
        $this->view->month = $month;
        $this->view->nameOFmonth = $this->times->convertNumberToMonthName($month);
        $this->view->currentUser = $user;
        $this->_redirect('time-tracker','team');
    }
}