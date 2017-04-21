<?php
namespace Org\WeiXin;
class Excel{
    /**
     * 本方法依赖 PHPExcel
     * @author zb
     * @param 内容数组 $columnArr 例如：：array(array('uid'=>1,'name'=>'小李'), array('uid'=>2,'name'=>'张山'));     
     * @param 列名数组 $columnName 例如：：array('uid','name');
     * @param 文件名  $tittle
     * @return 直接跳到下载页面
     */
    public function getExcel($columnArr,$columnName,$tittle="未命名"){
        Vendor('PHPExcel.PHPExcel');
        date_default_timezone_set('Asia/Shanghai');
        $objPHPExcel = new \PHPExcel();
        /*以下是一些设置 ，什么作者 标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("小马飞腾")
        ->setLastModifiedBy("小马飞腾")
        ->setTitle("数据EXCEL导出")
        ->setSubject("数据EXCEL导出")
        ->setDescription("备份数据")
        ->setKeywords("excel")
        ->setCategory("result file");
        /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $head=range('A','Z');
             //设置列名
            $numName=0;
            foreach ($columnName as $v){
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($head[$numName].'1',$v);
                $objPHPExcel->getActiveSheet()->getColumnDimension($head[$numName])->setAutoSize(true);
                $numName++;
              }
              $num=2;
            foreach($columnArr as $v){
                $numChildren=0;
                foreach ($v as $v1){
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($head[$numChildren].$num, $v1);
                        $numChildren++;
                    }
                    $num++;
            }
            //设置表名
            $objPHPExcel->getActiveSheet()->setTitle('User');
            //设置表的指针
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename={$tittle}.xls");
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
    }
}
    