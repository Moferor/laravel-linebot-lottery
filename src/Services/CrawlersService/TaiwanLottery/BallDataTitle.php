<?php


namespace Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery;


class BallDataTitle
{
    const NewJackpot = '最新累積頭獎金額';
    const NewNumber = '期別';
    const NewDate = '開獎日期';
    const NewBall = '落球順序';
    const NewBallSort = '大小順序';
    const NewSpecial = '特別號或第二區';
    const NewPeople = '本期中獎注數';
    const NewOneBonus = '本期頭獎單注獎金';
    const NewTwoBonus = '貳獎獎金';
    const NewTreeBonus = '參獎獎金';
    const NewFourBonus = '肆獎獎金';
    const NewFiveBonus = '伍獎獎金';
    const NewSixBonus = '陸獎獎金';
    const NewSevenBonus = '柒獎獎金';
    const NewEightBonus = '捌獎獎金';
    const NewNineBonus = '玖獎獎金';
    const NewTenBonus = '普獎獎金';
    const heardParam = '頭部樣板';
    const bodyParam = '身體樣板';
    const footerParam = '底部樣板';

    const BallDataTittleFunctionName = [
        //        self::NewJackpot =>'getJackpot',
        self::NewNumber => 'getNewNumber',
        self::NewDate => 'getNewDate',
        self::NewBall => 'getNewBalls',
        self::NewBallSort => 'getNewBallsSort',
        self::NewSpecial => 'getSpecial',
        self::NewPeople => 'getPeople',
        self::NewOneBonus => 'getOneBonus',
        self::NewTwoBonus => 'getTwoBonus',
        self::NewTreeBonus => 'getTreeBonus',
        self::NewFourBonus => 'getFourBonus',
        self::NewFiveBonus => 'getFiveBonus',
        self::NewSixBonus => 'getSixBonus',
        self::NewSevenBonus => 'getSevenBonus',
        self::NewEightBonus => 'getEightBonus',
        self::NewNineBonus => 'getNineBonus',
        self::NewTenBonus => 'getTenBonus'
    ];

}
