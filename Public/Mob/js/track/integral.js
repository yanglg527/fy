//我修改的条状图高度计算方法
//function drawBarChart(yData, me){
//    var yMax = 1;
//    $.each(yData,function(index,item){
//        if(item >　yMax){
//            yMax = item;
//        }
//    });
//    yMax = yMax * 2;
//    var avgYMax = parseInt(yMax / 6);
//    var yushuYMax = yMax % 6;
//    var y_axis=[avgYMax*5, avgYMax*4, avgYMax*3, avgYMax*2, avgYMax];//Y轴
//    console.log("y_axis = " + JSON.stringify(y_axis));
//    var x_axis=[0,500,1000,1500,5000];//X轴
//    var data=yData;//积分各阶段的人数
//    var me=me.score;//个人积分数
//    var html='';//画Y轴
//    $.each(y_axis,function(index,item){
//        html+='<div><i>'+item+'</i><span></span></div>';
//    });
//    $(".y-axial").html('<p>(人)</p>'+html);
////计算Y轴的间距
//    var space=$(".y-axial>div").height();
//    console.log("space = " + space);
//    var p_height='',p1='',p2='',p3='',p4='';
////判断我所处的阶段
//    if(me<=500){
//        p1='<span class="me"><span class="head">您处在的阶段</span><img src="__ROOT__"' + me.headimgurl + '></span>';
//    }else if(me>500&&me<=1000){
//        p2='<span class="me"><span class="head">您处在的阶段</span><img src="__ROOT__"' + me.headimgurl + '></span>';
//    }else if(me>1000&&me<=1500){
//        p3='<span class="me"><span class="head">您处在的阶段</span><img src="../../images/track/3.png"></span>';
//    }else if(me>1500){
//        p4='<span class="me"><span class="head">您处在的阶段</span><img src="../../images/track/3.png"></span>';
//    }
////柱状图p的高度
//    $.each(data,function(index,item){
//        var text=index==0?p1:index==1?p2:index==2?p3:p4;
//        p_height+='<div><p style="height:'+space*item/avgYMax+'px;" class="p'+(index+1)+'"><span>'+item+'</span>'+text+'</p></div>';
//    });
//    $(".x-axis").html(p_height);
////画X轴
//    var axis='';
//    $.each(x_axis,function(index,item){
//        if(index<x_axis.length-1){
//            axis+=' <div>'+item+'~'+x_axis[index+1]+'</div>';
//        }
//    });
//    $(".axis").html(axis+"<p>(分)</p>");
//    $("#myChart").attr("width",document.body.clientWidth-40);
//}

//    $(document).ready(function(){
//        var y_axis=[0,100,200,300,400,500].reverse();//Y轴
//        var x_axis=[0,500,1000,1500,5000];//X轴
//        var data=[200,380,287,100];//积分各阶段的人数
//        var me=800;//个人积分数
//        var html='';//画Y轴
//        $.each(y_axis,function(index,item){
//            html+='<div><i>'+(item+100)+'</i><span></span></div>';
//        });
//        $(".y-axial").html('<p>(人)</p>'+html);
////计算Y轴的间距
//        var space=$(".y-axial>div").height();
//        var p_height='',p1='',p2='',p3='',p4='';
////判断我所处的阶段
//        if(me<=500){
//            p1='<span class="me"><span class="head">您处在的阶段</span><img src="../../images/track/3.png"></span>';
//        }else if(me>500&&me<=1000){
//            p2='<span class="me"><span class="head">您处在的阶段</span><img src="../../images/track/3.png"></span>';
//        }else if(me>1000&&me<=1500){
//            p3='<span class="me"><span class="head">您处在的阶段</span><img src="../../images/track/3.png"></span>';
//        }else if(me>1500){
//            p4='<span class="me"><span class="head">您处在的阶段</span><img src="../../images/track/3.png"></span>';
//        }
////柱状图p的高度
//        $.each(data,function(index,item){
//            var text=index==0?p1:index==1?p2:index==2?p3:p4;
//            p_height+='<div><p style="height:'+space/100*item+'px;" class="p'+(index+1)+'"><span>'+item+'</span>'+text+'</p></div>';
//        });
//        $(".x-axis").html(p_height);
////画X轴
//        var axis='';
//        $.each(x_axis,function(index,item){
//            if(index<x_axis.length-1){
//                axis+=' <div>'+item+'~'+x_axis[index+1]+'</div>';
//            }
//        });
//        $(".axis").html(axis+"<p>(分)</p>");
//    });
//$("#myChart").attr("width",document.body.clientWidth-40);
//console.log($("#myChart").width());
//    曲线的数据
//var data = {
//    labels : ["一","二","三","四","五","六","日"],
//    datasets : [
//        {
//            fillColor : "transparent",
//            strokeColor : "#5ab3f1",
//            pointColor : "#5ab3f1",
//            pointStrokeColor : "#fff",
//            data : [25,50,60,70,65,40,90]
//        },
//        {
//            fillColor : "transparent",
//            strokeColor : "#ffb258",
//            pointColor : "#ffb258",
//            pointStrokeColor : "#fff",
//            data : [28,48,40,19,96,27,100]
//        },
//        {
//            fillColor : "transparent",
//            strokeColor : "#ce3d3a",
//            pointColor : "#ce3d3a",
//            pointStrokeColor : "#fff",
//            data : [65,59,90,81,56,55,40]
//        }
//    ]
//};
//    配置选项
var optinos={
    // 网格线是否在数据线的上面
    scaleOverlay : false,
    // 是否用硬编码重写y轴网格线
    scaleOverride : false,
    // x轴y轴的颜色
    scaleLineColor : "#c2c2c2",
    // x轴y轴的线宽
    scaleLineWidth : 1,
    // 是否显示y轴的标签
    scaleShowLabels : true,
    // 标签显示值
    scaleLabel : "<%=value%>",
    // 标签的字体
    scaleFontFamily : "'Arial'",
    // 标签字体的大小
    scaleFontSize :13,
    // 标签字体的样式
    scaleFontStyle : "normal",
    // 标签字体的颜色
    scaleFontColor : "#333",
    // 是否显示网格线
    scaleShowGridLines : true,
    // 网格线的颜色
    scaleGridLineColor : "#e2e2e2",
    // 网格线的线宽
    scaleGridLineWidth : 1,
    // 是否是曲线
    bezierCurve : true,
    // 是否显示点
    pointDot : true,
    // 点的半径
    pointDotRadius : 3,
    // 点的线宽
    pointDotStrokeWidth : 1,
    datasetStroke : true,
    // 数据线的线宽
    datasetStrokeWidth : 2,
    // 数据线是否填充颜色
    datasetFill : true,
    // 是否有动画效果
    animation : true,
    // 动画的步数
    animationSteps : 60,
    // 动画的效果
    animationEasing : "easeOutQuart",
    // 动画完成后调用
    onAnimationComplete : null
};
//var ctx = document.getElementById("myChart").getContext("2d");
//var myNewChart =new Chart(ctx).Line(data,optinos);
