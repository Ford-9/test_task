<?php

namespace football;

class CreateFootballPage
{
    private $full_array = array();
    private $team1 = array();
    private $team2 = array();
    private $messages = array();
    private $stadion = array();
    private $file_name = "";

    public function init($file_path){
        $getJson = $this->getJson($file_path);
        if ($getJson['error']) return $getJson;
        $this->parseDate();
        $this->createPage();

        $this->full_array = array();
        $this->team1 = array();
        $this->team2 = array();
        $this->messages = array();
        $this->stadion = array();
        $this->file_name = "";

        return array('error' => '0', 'disc' => 'Site successfully created!');
    }

    private function getJson($file_path){
        if(!file_exists($file_path)){
            return array('error' => '1', 'disc' => 'File not exist!');
        }
        $arr = json_decode(file_get_contents($file_path));
        if (is_array($arr)) {
            $this->full_array = $arr;
            $this->file_name = str_replace(".json", "", array_pop(explode("/", $file_path)));
            return array('error' => '0');
        } else {
            return array('error' => '1', 'disc' => 'File contains false data!');
        }
    }

    private function parseDate(){
        foreach ($this->full_array as $key => $value){
            switch ($value->type) {
                case 'info':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description);
                    break;
                case 'startPeriod':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description);
                    if($value->details == []) break;
                    $value = $value->details;
                    $this->stadion = array('county'=> $value->country, 'city' => $value->city, 'stadium' => $this->stadium);
                    
                    $team1 = $value->team1;
                    $this->team1 = array('title' => $team1->title, 'country' => $team1->country, 'coach' => $team1->coach, 'goals' => 0);
                    $players1 = array();
                    foreach ($team1->players as $player){
                        $players1[$player->number] = array('name' => $player->name,'goal' =>0, 'assist_goal' => 0, 'time_in_game' => 0);
                    }
                    foreach ($team1->startPlayerNumbers as $startPlayerNumber){
                        $players1[$startPlayerNumber]['startGame'] = 1;
                    }
                    $this->team1['players'] = $players1;

                    $team2 = $value->team2;
                    $this->team2 = array('title' => $team2->title, 'country' => $team2->country, 'coach' => $team2->coach, 'goals' => 0);
                    $players2 = array();
                    foreach ($team2->players as $player){
                        $players2[$player->number] = array('name' => $player->name,'goal' =>0, 'assist_goal' => 0, 'time_in_game' => 0);
                    }
                    foreach ($team2->startPlayerNumbers as $startPlayerNumber){
                        $players2[$startPlayerNumber]['startGame'] = 1;
                    }
                    $this->team2['players'] = $players2;
                    break;
                case 'finishPeriod':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description);
                    break;
                case 'dangerousMoment':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    break;
                case 'yellowCard':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    if($this->team1['title'] == $value->details->team){
                        $this->team1['players'][$value->details->playerNumber]['yellowCard'] = 1;
                    }else{
                        $this->team2['players'][$value->details->playerNumber]['yellowCard'] = 1;
                    }
                    break;
                case 'redCard':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    if($this->team1['title'] == $value->details->team){
                        $this->team1['players'][$value->details->playerNumber]['redCard'] = 1;
                        if($this->team1['players'][$value->details->playerNumber]['startGame']){
                            $this->team1['players'][$value->details->playerNumber]['time_in_game'] = $value->time;
                        }else{
                            $this->team1['players'][$value->details->playerNumber]['time_in_game'] = $value->time - $this->team1['players'][$value->details->playerNumber]['time_enter_in_game'];
                        }
                    }else{
                        $this->team2['players'][$value->details->playerNumber]['redCard'] = 1;
                        if($this->team2['players'][$value->details->playerNumber]['startGame']){
                            $this->team2['players'][$value->details->playerNumber]['time_in_game'] = $value->time;
                        }else{
                            $this->team2['players'][$value->details->playerNumber]['time_in_game'] = $value->time - $this->team2['players'][$value->details->playerNumber]['time_enter_in_game'];
                        }
                    }
                    break;
                case 'goal':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    if($this->team1['title'] == $value->details->team){
                        $this->team1['goals'] = $this->team1['goals'] + 1;
                        $this->team1['players'][$value->details->playerNumber]['goal'] = $this->team1['players'][$value->details->playerNumber]['goal'] + 1;
                        $this->team1['players'][$value->details->assistantNumber]['assist_goal'] = $this->team1['players'][$value->details->assistantNumber]['assist_goal'] + 1;
                    }else{
                        $this->team2['goals'] = $this->team2['goals'] + 1;
                        $this->team2['players'][$value->details->playerNumber]['goal'] = $this->team2['players'][$value->details->playerNumber]['goal'] + 1;
                        $this->team2['players'][$value->details->assistantNumber]['assist_goal'] = $this->team2['players'][$value->details->assistantNumber]['assist_goal'] + 1;
                    }
                    break;
                case 'replacePlayer':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description,
                        'important' => 1);
                    if($this->team1['title'] == $value->details->team){
                        $this->team1['players'][$value->details->inPlayerNumber]['replase'] = 1;
                        $this->team1['players'][$value->details->outPlayerNumber]['time_in_game'] = $value->time;
                        $this->team1['players'][$value->details->inPlayerNumber]['time_enter_in_game'] = $value->time;
                    }else{
                        $this->team2['players'][$value->details->inPlayerNumber]['replase'] = 1;
                        $this->team2['players'][$value->details->outPlayerNumber]['time_in_game'] = $value->time;
                        $this->team2['players'][$value->details->inPlayerNumber]['time_enter_in_game'] = $value->time;
                    }
                    break;
            }
        }

        $last_time = array_pop($this->messages)['time'];
        foreach($this->team1['players'] as $key => $value){
            if(!$value['time_in_game'] AND $value['startGame']){
                $this->team1['players'][$key]['time_in_game'] = $last_time;
            } else if($value['replase'] AND !$value['redCard']){
                $this->team1['players'][$key]['time_in_game'] = $last_time - $this->team1['players'][$key]['time_enter_in_game'];
            }
        }
        foreach($this->team2['players'] as $key => $value){
            if(!$value['time_in_game'] AND $value['startGame']){
                $this->team2['players'][$key]['time_in_game'] = $last_time;
            } else if($value['replase']){
                $this->team2['players'][$key]['time_in_game'] = $last_time - $this->team2['players'][$key]['time_enter_in_game'];
            }
        }
    }

    private function createPage(){
        $templateContent = file_get_contents("template.php");

        $templateContent = str_replace("%page_title%", $this->team1['title'].' - '.$this->team2['title'], $templateContent);
        $templateContent = str_replace("%team1_title%", $this->team1['title'], $templateContent);
        $templateContent = str_replace("%team1_country%", $this->team1['country'], $templateContent);
        $templateContent = str_replace("%team1_goals%", $this->team1['goals'], $templateContent);
        $templateContent = str_replace("%team2_title%", $this->team2['title'], $templateContent);
        $templateContent = str_replace("%team2_country%", $this->team2['country'], $templateContent);
        $templateContent = str_replace("%team2_goals%", $this->team2['goals'], $templateContent);

        $team1_startGame_players = "";
        $team1_replace_players = "";
        $team1_spare_players = "";
        foreach($this->team1['players'] as $key => $value){
            $class = "";
            if($value['yellowCard']){
                $class = 'style="background-color:yellow;"';
            }
            if($value['redCard']){
                $class = 'style="background-color:red;"';
            }
            if($value['startGame']){
                $team1_startGame_players .= "<tr ".$class."><td>".$key."</td><td>".$value['name']."</td><td>".$value['time_in_game']."</td><td>".$value['goal']."</td><td>".$value['assist_goal']."</td></tr>";
            }else if($value['replase']){
                $team1_replace_players .= "<tr ".$class."><td>".$key."</td><td>".$value['name']."</td><td>".$value['time_in_game']."</td><td>".$value['goal']."</td><td>".$value['assist_goal']."</td></tr>";
            }else{
                $team1_spare_players .= "<tr><td>".$key."</td><td>".$value['name']."</td></tr>";
            }
        };
        $templateContent = str_replace("%team1_startGame_players%", $team1_startGame_players, $templateContent);
        $templateContent = str_replace("%team1_replace_players%", $team1_replace_players, $templateContent);
        $templateContent = str_replace("%team1_spare_players%", $team1_spare_players, $templateContent);

        $team2_startGame_players = "";
        $team2_replace_players = "";
        $team2_spare_players = "";
        foreach($this->team2['players'] as $key => $value){
            $class = "";
            if($value['yellowCard']){
                $class = 'style="background-color:yellow;"';
            }
            if($value['redCard']){
                $class = 'style="background-color:red;"';
            }
            if($value['startGame']){
                $team2_startGame_players .= "<tr ".$class."><td>".$key."</td><td>".$value['name']."</td><td>".$value['time_in_game']."</td><td>".$value['goal']."</td><td>".$value['assist_goal']."</td></tr>";
            }else if($value['replase']){
                $team2_replace_players .= "<tr ".$class."><td>".$key."</td><td>".$value['name']."</td><td>".$value['time_in_game']."</td><td>".$value['goal']."</td><td>".$value['assist_goal']."</td></tr>";
            }else{
                $team2_spare_players .= "<tr><td>".$key."</td><td>".$value['name']."</td></tr>";
            }
        };
        $templateContent = str_replace("%team2_startGame_players%", $team2_startGame_players, $templateContent);
        $templateContent = str_replace("%team2_replace_players%", $team2_replace_players, $templateContent);
        $templateContent = str_replace("%team2_spare_players%", $team2_spare_players, $templateContent);

        $messages = "";
        foreach($this->messages as $key => $value){
            $messages .= "<tr><td>".$value['time']."</td><td>".$value['description']."</td></tr>";
        };
        $templateContent = str_replace("%messages%", $messages, $templateContent);

        $important = "";
        foreach($this->messages as $key => $value){
            if(!$value['important']) continue;
            $important .= "<tr><td>".$value['time']."</td><td>".$value['description']."</td></tr>";
        };
        $templateContent = str_replace("%important%", $important, $templateContent);

        $new_file_path = "result/".$this->file_name.".html";
        $file = fopen($new_file_path, 'w');
        fwrite($file, $templateContent);
        fclose($file);
    }
}

$football = new CreateFootballPage();
$res = $football->init('source/matches/1024102.json');

echo $res['disc'];