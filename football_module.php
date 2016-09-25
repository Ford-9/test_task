<?php

namespace football;

class CreateFootballPage
{
    private $full_array = array();
    private $teams = array();
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
                    $this->parseTeam($value->team1);
                    $this->parseTeam($value->team2);
                    break;
                case 'finishPeriod':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description);
                    break;
                case 'dangerousMoment':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    break;
                case 'yellowCard':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    $this->teams[$value->details->team]['players'][$value->details->playerNumber]['yellowCard'] = 1;
                    break;
                case 'redCard':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    $this->teams[$value->details->team]['players'][$value->details->playerNumber]['redCard'] = 1;
                    if($this->teams[$value->details->team]['players'][$value->details->playerNumber]['startGame']){
                        $this->teamteams[$value->details->team]['players'][$value->details->playerNumber]['time_in_game'] = $value->time;
                    }else{
                        $this->teamteams[$value->details->team]['players'][$value->details->playerNumber]['time_in_game'] = $value->time - $this->teamteams[$value->details->team]['players'][$value->details->playerNumber]['time_enter_in_game'];
                    }
                    break;
                case 'goal':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    $this->teams[$value->details->team]['goals'] = $this->teams[$value->details->team]['goals'] + 1;
                    $this->teams[$value->details->team]['players'][$value->details->playerNumber]['goal'] = $this->teams[$value->details->team]['players'][$value->details->playerNumber]['goal'] + 1;
                    $this->teams[$value->details->team]['players'][$value->details->assistantNumber]['assist_goal'] = $this->teams[$value->details->team]['players'][$value->details->assistantNumber]['assist_goal'] + 1;
                    break;
                case 'replacePlayer':
                    $this->messages[] = array('time' => $value->time, 'description' => $value->description, 'important' => 1);
                    $this->teams[$value->details->team]['players'][$value->details->inPlayerNumber]['replase'] = 1;
                    $this->teams[$value->details->team]['players'][$value->details->outPlayerNumber]['time_in_game'] = $value->time;
                    $this->teams[$value->details->team]['players'][$value->details->inPlayerNumber]['time_enter_in_game'] = $value->time;
                    break;
            }
        }

        $last_time = array_pop($this->messages)['time'];
        foreach($this->teams as $key => $value){
            foreach($value['players'] as $player_num => $player){
                if(!$player['time_in_game'] AND $player['startGame']){
                    $this->teams[$key]['players'][$player_num]['time_in_game'] = $last_time;
                } else if($player['replase'] AND !$player['redCard']){
                    $this->teams[$key]['players'][$player_num]['time_in_game'] = $last_time - $this->teams[$key]['players'][$player_num]['time_enter_in_game'];
                }
            }
        }
    }
    
    private function parseTeam($team){
        $players1 = array();
        foreach ($team->players as $player){
            $players1[$player->number] = array('name' => $player->name,'goal' =>0, 'assist_goal' => 0, 'time_in_game' => 0);
        }
        foreach ($team->startPlayerNumbers as $startPlayerNumber){
            $players1[$startPlayerNumber]['startGame'] = 1;
        }
        $this->teams[$team->title] =  array('country' => $team->country, 'coach' => $team->coach, 'goals' => 0, 'players' => $players1);
    }

    private function str_replace_once($search, $replace, $text){
        $pos = strpos($text, $search);
        return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }

    private function createPage(){
        $templateContent = file_get_contents("template.php");
        foreach($this->teams as $title => $team){
            $templateContent = $this->str_replace_once("%page_title%", $title, $templateContent);
            $templateContent = $this->str_replace_once("%team_title%", $title, $templateContent);
            $templateContent = $this->str_replace_once("%team_country%", $this->teams[$title]['country'], $templateContent);
            $templateContent = $this->str_replace_once("%team_goals%", $this->teams[$title]['goals'], $templateContent);

            $teams_startGame_players = "";
            $teams_replace_players = "";
            $teams_spare_players = "";
            foreach($this->teams[$title]['players'] as $key => $value){
                $class = "";
                if($value['yellowCard']){
                    $class = 'style="background-color:yellow;"';
                }
                if($value['redCard']){
                    $class = 'style="background-color:red;"';
                }
                if($value['startGame']){
                    $teams_startGame_players .= "<tr ".$class."><td>".$key."</td><td>".$value['name']."</td><td>".$value['time_in_game']."</td><td>".$value['goal']."</td><td>".$value['assist_goal']."</td></tr>";
                }else if($value['replase']){
                    $teams_replace_players .= "<tr ".$class."><td>".$key."</td><td>".$value['name']."</td><td>".$value['time_in_game']."</td><td>".$value['goal']."</td><td>".$value['assist_goal']."</td></tr>";
                }else{
                    $teams_spare_players .= "<tr><td>".$key."</td><td>".$value['name']."</td></tr>";
                }
            }
            $templateContent = $this->str_replace_once("%team_startGame_players%", $teams_startGame_players, $templateContent);
            $templateContent = $this->str_replace_once("%team_replace_players%", $teams_replace_players, $templateContent);
            $templateContent = $this->str_replace_once("%team_spare_players%", $teams_spare_players, $templateContent);
        }

        $important = "";
        $messages = "";
        foreach($this->messages as $key => $value){
            $messages .= "<tr><td>".$value['time']."</td><td>".$value['description']."</td></tr>";
            if($value['important']){
                $important .= "<tr><td>".$value['time']."</td><td>".$value['description']."</td></tr>";
            }
        };
        $templateContent = str_replace("%messages%", $messages, $templateContent);
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