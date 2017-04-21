<?php

namespace App\Client;
use Requests;
use Requests_Response as Response;
use Exception;

/**
 * Created by PhpStorm.
 * User: eric
 * Date: 17-4-19
 * Time: 上午1:01
 */
class QiutanClient
{

    private $timeout = 10000;

    private $proxy_host = '106.75.79.132';

    private $proxy_port = 31280;

    function __construct()
    {
    }

    /******
     * 足球数据， 增强型比分
     */

    public function todayMatches()
    {
        $url = "http://interface.win007.com/zq/today.aspx";

        $data = $this->request($url, [], 'xml');
        $matches = [];
        foreach ($data->match as $xml) {
            $matches[] = json_decode(json_encode($xml));
        }
        // TODO modify data key-value
        return $matches;
    }


    public function updatedMatches()
    {
        $url = "http://interface.win007.com/zq/change.xml";

        $url = "http://interface.win007.com/zq/change2.xml";
        $data = $this->request($url, [], 'xml');
        $matches = [];
        foreach ($data->h as $xml) {
            $matches[] = json_decode(json_encode($xml), true)[0];
        }
        // TODO modify data key-value
        return $matches;
    }


    public function todayMatchEvents()
    {
        $url = "http://interface.win007.com/zq/detail.aspx";
        $data = $this->request($url, [], null);

        $data = explode(';', $data);
        // TODO modify data key-value
        return $data;

    }

    public function matchesByIds($ids)
    {
        $url = "http://interface.win007.com/zq/BF_XMLByID.aspx";
        $params = ['id' => $ids];
        $data = $this->request($url);

        // TODO modify data key-value
        return $data;
    }

    public function matchesByDate($date)
    {
        $params = ['date' => $date];
        return $this->matches($params);
    }

    public function matchesByCompetition($competitionId)
    {
        $params = ['sclassID' => $competitionId];
        return $this->matches($params);
    }

    public function matches($params)
    {
        $url = "http://interface.win007.com/zq/detail.aspx";
        $data = $this->request($url, $params);

        // TODO modify data key-value
        return $data;
    }

    public function competitions()
    {
        $url = "http://interface.win007.com/zq/League_XML.aspx";
        $data = $this->request($url);

        // TODO modify data key-value
        return $data;

    }

    public function teams()
    {
        $url = "http://interface.win007.com/zq/Team_XML.aspx";
        $data = $this->request($url);

        // TODO modify data key-value
        return $data;
    }

    public function matchEvents($date)
    {
        $url = "http://interface.win007.com/zq/Event_XML.aspx";

        $params = ['date' => $date];
        $data = $this->request($url, $params);

        // TODO modify data key-value
        return $data;
    }

    //一周内
    public function matchTeamStatistics($date)
    {
        $url = "http://interface.win007.com/zq/Technic_XML.aspx";

        $params = ['date' => $date];
        $data = $this->request($url, $params);

        // TODO modify data key-value
        return $data;
    }


    public function scoreboards($competitionId, $subId)
    {
        $url = "http://interface.win007.com/zq/jifen.aspx";

        $params = [
            'ID' => $competitionId,
            'subId' => $subId,
        ];
        $data = $this->request($url, $params);

        // TODO modify data key-value
        return $data;
    }


    public function matchLineups($matchIds = [])
    {
        $url = "http://interface.win007.com/zq/lineup.aspx?";
        // TODO count(match ids) post / get
        $params = ['id' => implode(',', $matchIds)];
        $data = $this->request($url, $params);;

        // TODO modify data key-value
        return $data;
    }


    public function matchInjuries($matchIds = [])
    {
        $url = "http://interface.win007.com/zq/Injury.aspx?ID=比赛ID";
        // TODO count(match ids) post / get
        $params = ['id' => implode(',', $matchIds)];
        $data = $this->request($url, $params);;

        // TODO modify data key-value
        return $data;
    }


    /**
     * 8小时内的赛程删除、比赛时间修改记录
     */
    public function modifiedMatches()
    {
        $url = "http://interface.win007.com/zq/ModifyRecord.aspx";
    }


    public function liveText()
    {
        $url = "http://interface.win007.com/zq/TextLive.aspx";

    }

    public function lotteries()
    {

        $url = "http://www.310win.com/handle/MatchidInterface.aspx";
    }


    public function updatedPersons($days)
    {

    }

    public function personsByTeam($teamIds)
    {

    }


    public function matchReferees()
    {

    }


    public function matchPersonStatistics($matchIds)
    {

    }


    public function subLeagues()
    {

    }


    /*****
     * 足球数据仅欧赔
     */

    public function baijiaEuroOdds()
    {
        $url = 'http://interface.win007.com/zq/1x2.aspx';
    }


    /**
     * 即时赔率
     */
    public function odds()
    {
        $url = "http://interface.win007.com/zq/odds.aspx";
    }


    public function updatedOdds()
    {

        $url = "http://interface.win007.com/zq/ch_odds.xml";

    }


    // 历史同赔
    public function historyOdds()
    {
        $url = "http://interface.win007.com/zq/ch_odds.xml";
    }


    // 多盘口赔率借口
    public function multOdds()
    {
        $url = "http://interface.win007.com/zq/ch_odds.xml";
    }


    public function updatedMultOdds()
    {
        $url = "http://interface.win007.com/zq/ch_odds_m.xml";
    }


    public function halfEuroOdds()
    {
        $url = 'http://interface.win007.com/zq/Odds_1x2_half.aspx';
    }

    public function runningOdds()
    {
        $url = 'http://interface.win007.com/zq/Odds_Running.aspx';
    }


    public function specialOdds($type)
    {
        $url = 'http://interface.win007.com/zq/SpecialOdds.aspx?type=goalorder';

    }



    public function request($uri, array $data = [], $responseType = 'array')
    {
        $try = 1;
        do {
            $url = (starts_with($uri, 'http') ? $uri : (self::BASE_URL . $uri)) . '?' . http_build_query($data);
            try {
                $options = ['timeout' => $this->timeout];
                $options['proxy'] = $this->proxy_host . ":" . $this->proxy_port;
                $response = Requests::get($url, [], $options);
                if ($response->success) {
                    $data = $this->parse($response, $responseType);
                    if (!$data) {
                        \Log::error("API Access Failed, ", ['url' => $url, 'message' => $response->body]);
                        throw new Exception('API Aceess Fail', 0);
                    }
                    return $data;
                }
                $try++;
                sleep(1);
            } catch (Exception $e) {
                $try++;
            }

        } while ($try <= 3);

        throw new Exception('API Aceess Fail', 0);
    }

    public function parse(Response $response, $responseType = 'array')
    {
        $result = $response->body;
        if (in_array($responseType, ['array', 'xml'])) {
            $result = @simplexml_load_string($result, null, LIBXML_NOCDATA);
        }

        if ($responseType == 'array') {
            $result = $this->xml2array($result);
        }

        return $result;
    }

    public function xml2array($result) {
        $arr = @json_decode(@json_encode($result), TRUE);

        return $this->filterEmpty($arr);
    }

    private function filterEmpty($arr) {
        foreach ($arr as $k => $v) {
            if (is_array($v)){
                $arr[$k] = $this->filterEmpty($v);
            } else {
                if (trim($v) == '') {
                    unset($arr[$k]);
                }
            }
        }

        return $arr;
    }

}