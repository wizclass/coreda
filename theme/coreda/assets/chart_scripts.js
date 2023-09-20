var title_color = '';
var bar_color = [];
var circle_color = [];
var colors = [];
var bar_title = '';


if(Theme == 'white') {
  title_color = '#333';
  bar_color = ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000'];
  circle_color = ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000'];
  colors = ['#f8b874','#FD6585','#EE9AE5','#8959f9','#8959f9','#4C83FF','#2ad0fa','#0c51cf','#1fcf77','#26d4bd'];
} else if(Theme == 'dark') {
  title_color = '#fff';
  bar_color = ['#2d7dcb', '#508a9f', '#5b6066', '#db2eb3','#266099'];
  circle_color = ['#2d7dcb', '#508a9f', '#5b6066', '#db2eb3','#266099'];
  colors = ['#2d7dcb','#004e99','#287d9b','#034d64','#414d5a','#252e38','#733f88','#2f568f'];
} 

$('#mode_select').on('change',function(e) {
  mode_colorset(this.value);
});

$('#gnb_language a').on('click',function() {
  lang_change($(this).attr('data-num'));
})

if(getCookie('googtrans') == '/ko/en') {
  mega_title = 'MEGAPOOL BONUS';
  zeta_title = 'ZETAPOOL BONUS';
  zetaplus_title = 'Zeta + Full Bonus';
  super_title = 'SUPER BONUS';
  bonus_title = 'BONUS HASH';
  sub_title = 'Target Hash: 300%';
  sub_title2 = 'Target Hash: 100%';
} else if(getCookie('googtrans') == '/ko/zh-CN') {
  mega_title = '超级矿池奖金';
  zeta_title = 'Zetapool奖金';
  zetaplus_title = 'Zeta + 全额奖金';
  super_title = '超级奖金';
  bonus_title = '奖金哈希';
  sub_title = '目标哈希：300%';
  sub_title2 = '目标哈希：100%';
} else if(getCookie('googtrans') == '/ko/vi') {
  mega_title = 'Tiền thưởng Megapool';
  zeta_title = 'Tiền thưởng Zetapool';
  zetaplus_title = 'Zeta + Tiền thưởng đầy đủ';
  super_title = 'siêu tiền thưởng';
  bonus_title = 'băm thưởng';
  sub_title = 'Băm mục tiêu: 300%';
  sub_title = 'Băm mục tiêu: 100%';
} else if(getCookie('googtrans') == '/ko/ja') {
  mega_title = 'メガフルボーナス';
  zeta_title = 'ゼタフルボーナス';
  zetaplus_title = 'ゼータ+フルボーナス';
  super_title = 'スーパーボーナス';
  bonus_title = 'ボーナスハッシュ';
  sub_title = '目標ハッシュ：300％';
  sub_title = '目標ハッシュ：100％';
} else {
  mega_title = '메가풀 보너스';
  zeta_title = '제타풀 보너스';
  zetaplus_title = '제타+풀 보너스';
  super_title = '슈퍼 보너스';
  bonus_title = '보너스 해시';
  sub_title = '목표해시: 300%';
  sub_title2 = '목표해시: 100%';
}

function lang_change(num) {
  var mega_title = ['메가풀 보너스','MEGAPOOL BONUS','超级矿池奖金','Tiền thưởng Megapool','メガフルボーナス'];
  var zeta_title = ['제타풀 보너스','ZETAPOOL BONUS','Zetapool奖金','Tiền thưởng Zetapool','ゼタフルボーナス'];
  var zetaplus_title = ['제타+풀 보너스','Zeta + Full Bonus','Zeta + 全额奖金','Zeta + Tiền thưởng đầy đủ','ゼータ+フルボーナス'];
  var super_title = ['슈퍼 보너스','SUPER BONUS','超级奖金','siêu tiền thưởng','スーパーボーナス'];
  var bonus_title = ['보너스 해시','BONUS HASH','奖金哈希','băm thưởng','ボーナスハッシュ'];

  megachart.updateOptions({
    title: {
      text: mega_title[num]
    },
    subtitle: {
      text: sub_title,
    },
    xaxis: {
      categories: [bonus_title[num]],
    }
  });

  zetachart.updateOptions({
    title: {
      text: zeta_title[num]
    },
    xaxis: {
      categories: [bonus_title[num]],
    }
  });

  zetapluschart.updateOptions({
    title: {
      text: zetaplus_title[num]
    },
    subtitle: {
      text: sub_title,
    },
    xaxis: {
      categories: [bonus_title[num]],
    },
  });

  superchart.updateOptions({
    title: {
      text: super_title[num]
    },
    subtitle: {
      text: sub_title,
    },
    xaxis: {
      categories: [bonus_title[num]],
    }
  });
}

window.Apex = {
    chart: {
      foreColor: '#ccc',
      toolbar: {
        show: false
      },
    },
    stroke: {
      width: 3
    },
    dataLabels: {
      enabled: false
    },
    tooltip: {
      theme: 'dark'
    },
    grid: {
      borderColor: "#535A6C",
      xaxis: {
        lines: {
          show: true
        }
      }
    }
  };

  
  var spark1 = {
    chart: {
      id: 'spark1',
      group: 'sparks',
      type: 'line',
      height: 80,
      inverseOrder: true,
      sparkline: {
        enabled: true
      },
      dropShadow: {
        enabled: true,
        top: 1,
        left: 1,
        blur: 2,
        opacity: 0.2,
      }
    },
    series: [{
      data: dataset(chartData,'hash_info','mega','',0.01)
    }],
    xaxis: {
        categories: dataset(chartData,'date')
    },
    stroke: {
      curve: 'smooth'
    },
    markers: {
      size: 0
    },
    grid: {
      padding: {
        top: 20,
        bottom: 10,
        left: 110
      }
    },
    colors: ['#fff'],
    tooltip: {
      x: {
        show: true,
        title: {
            formatter: function formatter(val) {
              return '';
            }
        }
      },
      y: {
        title: {
          formatter: function formatter(val) {
            return '';
          }
        }
      }
    }
  }
  
  var spark2 = {
    chart: {
      id: 'spark2',
      group: 'sparks',
      type: 'line',
      height: 80,
      sparkline: {
        enabled: true
      },
      dropShadow: {
        enabled: true,
        top: 1,
        left: 1,
        blur: 2,
        opacity: 0.2,
      }
    },
    series: [{
      data: dataset(chartData,'hash_info','zeta','',0.01)
    }],
    stroke: {
      curve: 'smooth'
    },
    grid: {
      padding: {
        top: 20,
        bottom: 10,
        left: 110
      }
    },
    markers: {
      size: 0
    },
    colors: ['#fff'],
    xaxis: {
        categories: dataset(chartData,'date')
    },
    tooltip: {
      x: {
        show: true,
        title: {
            formatter: function formatter(val) {
              return '';
            }
          }
      },
      y: {
        title: {
          formatter: function formatter(val) {
            return '';
          }
        }
      }
    }
  }
  
  var spark3 = {
    chart: {
      id: 'spark3',
      group: 'sparks',
      type: 'line',
      height: 80,
      sparkline: {
        enabled: true
      },
      dropShadow: {
        enabled: true,
        top: 1,
        left: 1,
        blur: 2,
        opacity: 0.2,
      }
    },
    series: [{
      data: dataset(chartData,'hash_info','zetaplus','',0.01)
    }],
    stroke: {
      curve: 'smooth'
    },
    markers: {
      size: 0
    },
    
    grid: {
      padding: {
        top: 20,
        bottom: 10,
        left: 110
      }
    },
    colors: ['#fff'],
   
    xaxis: {
    categories: dataset(chartData,'date'),
      crosshairs: {
        width: 1
      },
    },
    tooltip: {
      x: {
        show: true,
        title: {
            formatter: function formatter(val) {
              return '';
            }
          }
      },
      y: {
        title: {
          formatter: function formatter(val) {
            return '';
          }
        }
      }
    }
  }
  
  var spark4 = {
    chart: {
      id: 'spark4',
      group: 'sparks',
      type: 'line',
      height: 80,
      sparkline: {
        enabled: true
      },
      dropShadow: {
        enabled: true,
        top: 1,
        left: 1,
        blur: 2,
        opacity: 0.2,
      }
    },
    series: [{
      data: dataset(chartData,'hash_info','super','',0.01)
    }],
    stroke: {
      curve: 'smooth'
    },
    markers: {
      size: 0
    },
    grid: {
      padding: {
        top: 20,
        bottom: 10,
        left: 110
      }
    },
    colors: ['#fff'],
    xaxis: {
      categories: dataset(chartData,'date'),
      crosshairs: {
        width: 1
      },
    },
    tooltip: {
        x: {
            show: true,
            title: {
                formatter: function formatter(val) {
                  return '';
                }
              }
          },
      y: {
        title: {
          formatter: function formatter(val) {
            return '';
          }
        }
      }
    }
  }

  var spark5 = {
    chart: {
      id: 'spark5',
      group: 'sparks',
      type: 'line',
      height: 80,
      inverseOrder: true,
      sparkline: {
        enabled: true
      },
      dropShadow: {
        enabled: true,
        top: 1,
        left: 1,
        blur: 2,
        opacity: 0.2,
      }
    },
    series: [{
    //   data: dataset(miningData,'mining')
        data: dataset(chartData,'hash_info','all','',0.01)
    }],
    xaxis: {
        categories: dataset(miningData,'day')
    },
    stroke: {
      curve: 'smooth'
    },
    markers: {
      size: 0
    },
    grid: {
      padding: {
        top: 20,
        bottom: 10,
        left: 110
      }
    },
    colors: ['#fff'],
    tooltip: {
      x: {
        show: true,
        title: {
            formatter: function formatter(val) {
              return '';
            }
        }
      },
      y: {
        title: {
          formatter: function formatter(val) {
            return '';
          }
        }
      }
    }
  }



  var chart_circle = {
    series: bonusData,
    colors: circle_color,
    labels: ['Mega', 'Zeta', 'ZetaPlus', 'Super','My Mining'],
    chart: {
    height: 390,
    type: 'radialBar',
    },
    plotOptions: {
        radialBar: {
            offsetY: 0,
            startAngle: 0,
            endAngle: 270,
            hollow: {
                margin: 5,
                size: '30%',
                background: 'transparent'
            },
            dataLabels: {
                name: {
                show: true,
                },
                value: {
                show: true,
                }
            }
        }
    },
    stroke: {
        lineCap: 'round'
    },
    legend: {
        show: true,
        floating: true,
        fontSize: '14px',
        position: 'left',
        offsetX: 0,
        offsetY: 5,
        labels: {
        useSeriesColors: true,
        },
        formatter: function(seriesName, opts) {
        return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]+"%"
        },
        itemMargin: {
        vertical: 3
        }
    },
    responsive: [{
        breakpoint: 480,
        options: {
        legend: {
            show: true
        }
        }
    }]  
};

  
  
  new ApexCharts(document.querySelector("#spark1"), spark1).render();
  new ApexCharts(document.querySelector("#spark2"), spark2).render();
  new ApexCharts(document.querySelector("#spark3"), spark3).render();
  new ApexCharts(document.querySelector("#spark4"), spark4).render();
  new ApexCharts(document.querySelector("#spark5"), spark5).render();


  var container_circle = new ApexCharts(document.querySelector("#circleChart"), chart_circle);
  container_circle.render();



  var barchart = {
    series: [{
    name: 'Mega',
    data: dataset(megaData,'mining')
  }, {
    name: 'Zeta',
    data: dataset(zetaData,'mining')
  }, {
    name: 'Zeta Plus',
    data: dataset(zetaplusData,'mining')
  },{
    name: 'Super',
    data: dataset(superData,'mining')
  },{
    name: 'my',
    data: dataset(miningData,'mining')
  }],
    chart: {
    type: 'bar',
    height: 400
  },
  colors: bar_color,
  plotOptions: {
    bar: {
      horizontal: true,
      columnWidth: '100%',
      endingShape: 'rounded',
      borderRadius: 4,
    },
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['transparent']
  },
  xaxis: {
    categories: dataset(chartData,'date'),
    decimalsInFloat : true
  },
  yaxis: {
    /* title: {
      text: 'Mining Bonus'
    } */
  },
  grid: {
    borderColor: "#888",
    xaxis: {
      lines: {
        show: false
      }
    }
  },
  fill: {
    opacity: 0.8
  },
  tooltip: {
    y: {
      formatter: function (val) {
        return val + " ETH"
      }
    }
  }
  };

  var chart = new ApexCharts(document.querySelector("#mychart"), barchart);
  chart.render();


  var recom_data = dataset(chartData,'recom_info','hash_10');
  var mega_spark1 = {
    chart: {
        id: 'mega_sparkline1',
        group: 'mega_sparklines',
        type: 'area',
        height: 160,
        sparkline: {
          enabled: true
        },
      },
      stroke: {
        curve: 'straight'
      },
      fill: {
        opacity: 1,
      },
      series: [{
        name: '10 layer Hash', 
        data: recom_data
      }],
      labels: dataset(chartData,'date'),
        //labels: [...Array(5).keys()].map(n => `2020-09-0${n+1}`),
      colors: ['#DCE6EC'],
      title: {
        text: Price(recom_data[recom_data.length -1]),
        offsetX: 0,
        offsetY: 20,
        style: {
          fontSize: '24px',
          cssClass: 'apexcharts-yaxis-title'
        }
      },
      subtitle: {
        text: '10 layer Hash',
        offsetX: 0,
        offsetY: 50,
        style: {
          fontSize: '14px',
          cssClass: 'apexcharts-yaxis-title'
        }
      }
  }
  var chart_mega_spark1 = new ApexCharts(document.querySelector("#megaspark1"), mega_spark1)
  
  var mega_chart = {
    chart: {
      id : megachart,
      height: 100,
      type: 'bar',
      stacked: true,
      sparkline: {
        enabled: true
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '45%',
        colors: {
          backgroundBarColors: ['#ddd']
        },
        borderRadius: 4,
      },
    },
    colors: colors[0],
    stroke: {
      width: 0,
      lineCap: 'round'
    },
    series: [{
      name: '메가풀',
      data: [bonusData[0]]
    }],
    fill: {
      type: 'gradient',
      gradient: {
        gradientToColors: [colors[1]]
      }
    },
    title: {
      floating: true,
      offsetX: 0,
      offsetY: 0,
      text: mega_title,
      style: {
        fontSize: '16px',
        color: title_color
      }
    },
    subtitle: {
      floating: true,
      align: 'right',
      offsetY: 15,
      offsetx: 10,
      text: sub_title,
      style: {
        fontSize: '13px',
        color: '#999'
      }
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: '16px'
        },
        offsetX: 15,
        formatter: function (val, opt) {
            return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val + "%"
        },
        textAnchor: 'start',
        dropShadow: {
          enabled: true,
          top: 1,
          left: 1,
          blur: 1,
          color: '#000',
          opacity: 0.45
      }
    },
    tooltip: {
      enabled: false
    },
    xaxis: {
      categories: [bonus_title],
    },
    yaxis: {
      max:300
    },
  }
  var megachart = new ApexCharts(document.querySelector("#megachart"), mega_chart);
  

//   제타마이닝

  var brecom_data = dataset(chartData,'brecom_info','hash_10');
  var zeta_spark1 = {
    chart: {
        id: 'mega_sparkline1',
        group: 'mega_sparklines',
        type: 'area',
        height: 160,
        sparkline: {
          enabled: true
        },
      },
      stroke: {
        curve: 'straight'
      },
      fill: {
        opacity: 1,
      },
      series: [{
        name: '10 layer Hash', 
        data: brecom_data
      }],
      labels: dataset(chartData,'date'),
        //labels: [...Array(5).keys()].map(n => `2020-09-0${n+1}`),
      colors: ['#DCE6EC','#ff6600'],
      title: {
        text: Price(brecom_data[brecom_data.length -1]),
        offsetX: 0,
        offsetY: 20,
        style: {
          fontSize: '24px',
          cssClass: 'apexcharts-yaxis-title'
        }
      },
      subtitle: {
        text: '10 layer Hash',
        offsetX: 0,
        offsetY: 50,
        style: {
          fontSize: '14px',
          cssClass: 'apexcharts-yaxis-title'
        }
      }
  }
  var chart_zeta_spark1 = new ApexCharts(document.querySelector("#zetaspark1"), zeta_spark1)

  var zeta_chart = {
    chart: {
      id : zetachart,
      height: 100,
      type: 'bar',
      stacked: true,
      sparkline: {
        enabled: true
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '45%',
        colors: {
          backgroundBarColors: ['#ddd']
        },
        borderRadius: 4,
      },
    },
    colors: colors[2],
    stroke: {
      width: 0,
      lineCap: 'round'
    },
    series: [{
      name: '제타풀',
      data: [bonusData[1]]
    }],
    fill: {
      type: 'gradient',
      gradient: {
        gradientToColors: [colors[3]]
      }
    },
    title: {
      floating: true,
      offsetX: 0,
      offsetY: 0,
      text: zeta_title,
      style: {
        fontSize: '16px',
        color: title_color
      }
    },
    subtitle: {
      floating: true,
      align: 'right',
      offsetY: 15,
      offsetx: 10,
      text: sub_title,
      style: {
        fontSize: '13px',
        color: '#999'
      }
    },
    dataLabels: {
        enabled: true,
        offsetX: 0,
        style: {
            fontSize: '16px'
        },
        formatter: function (val, opt) {
            return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val + "%"
        },
        textAnchor: 'start',
        dropShadow: {
          enabled: true,
          top: 1,
          left: 1,
          blur: 1,
          color: '#000',
          opacity: 0.45
      }
    },
    tooltip: {
      enabled: false
    },
    xaxis: {
      categories: [[bonus_title]],
    },
    yaxis: {
      max:300
    },
  }
  var zetachart = new ApexCharts(document.querySelector("#zetachart"), zeta_chart);
  


  
//   제타플러스

  var brecom2_data = dataset(chartData,'brecom2_info','hash_10');
  var zetaplus_spark1 = {
    chart: {
        id: 'zetaplus_sparkline1',
        type: 'area',
        height: 160,
        sparkline: {
          enabled: true
        },
      },
      stroke: {
        curve: 'straight'
      },
      fill: {
        opacity: 1,
      },
      series: [{
        name: '10 layer Hash', 
        data: brecom2_data
      }],
      labels: dataset(chartData,'date'),
        //labels: [...Array(5).keys()].map(n => `2020-09-0${n+1}`),
      colors: ['#DCE6EC'],
      title: {
        text: Price(brecom2_data[brecom2_data.length -1]),
        offsetX: 0,
        offsetY: 20,
        style: {
          fontSize: '24px',
          cssClass: 'apexcharts-yaxis-title'
        }
      },
      subtitle: {
        text: '10 layer Hash',
        offsetX: 0,
        offsetY: 50,
        style: {
          fontSize: '14px',
          cssClass: 'apexcharts-yaxis-title'
        }
      }
  }
  var chart_zetaplus_spark1 = new ApexCharts(document.querySelector("#zetaplusspark1"), zetaplus_spark1)

  var zetaplus_chart = {
    chart: {
      id : zetapluschart,
      height: 100,
      type: 'bar',
      stacked: true,
      sparkline: {
        enabled: true
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '45%',
        colors: {
          backgroundBarColors: ['#ddd']
        },
        borderRadius: 4,
      },
    },
    colors: colors[4],
    stroke: {
      width: 0,
      lineCap: 'round'
    },
    series: [{
      name: '제타+풀',
      data: [bonusData[2]]
    }],
    fill: {
      type: 'gradient',
      gradient: {
        gradientToColors: [colors[5]]
      }
    },
    title: {
      floating: true,
      offsetX: 0,
      offsetY: 0,
      text: zetaplus_title,
      style: {
        fontSize: '16px',
        color: title_color
      }
    },
    subtitle: {
      floating: true,
      align: 'right',
      offsetY: 15,
      offsetx: 10,
      text: sub_title,
      style: {
        fontSize: '13px',
        color: '#999'
      }
    },
    dataLabels: {
        enabled: true,
        offsetX: 15,
        style: {
            fontSize: '16px'
        },
        formatter: function (val, opt) {
            return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val + "%"
        },
        textAnchor: 'start',
        dropShadow: {
          enabled: true,
          top: 1,
          left: 1,
          blur: 1,
          color: '#000',
          opacity: 0.45
      }
    },
    tooltip: {
      enabled: false
    },
    xaxis: {
      categories: [[bonus_title]],
    },
    yaxis: {
      max: 300
    },
  }
  var zetapluschart = new ApexCharts(document.querySelector("#zetapluschart"), zetaplus_chart);



  
// 슈퍼

  if(bonusData[3] > 100){
      var superbonus_percent = 100;
  }else{
        var superbonus_percent = bonusData[3];
  }
  var super_hash_data = dataset(chartData,'hash_info','super');
  var super_spark1 = {
    chart: {
        id: 'super_sparkline1',
        type: 'area',
        height: 160,
        sparkline: {
          enabled: true
        },
      },
      stroke: {
        curve: 'straight'
      },
      fill: {
        opacity: 1,
      },
      series: [{
        name: 'Super Hash', 
        data: super_hash_data
      }],
      labels: dataset(chartData,'date'),
        //labels: [...Array(5).keys()].map(n => `2020-09-0${n+1}`),
      colors: ['#DCE6EC'],
      title: {
        text: Price(super_hash_data[super_hash_data.length -1]),
        offsetX: 0,
        offsetY: 20,
        style: {
          fontSize: '24px',
          cssClass: 'apexcharts-yaxis-title'
        }
      },
      subtitle: {
        text: 'Super Hash',
        offsetX: 0,
        offsetY: 50,
        style: {
          fontSize: '14px',
          cssClass: 'apexcharts-yaxis-title'
        }
      }
  }
  var chart_super_spark1 = new ApexCharts(document.querySelector("#superspark1"), super_spark1)

  var super_chart = {
    chart: {
      id : superchart,
      height: 100,
      type: 'bar',
      stacked: true,
      sparkline: {
        enabled: true
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '45%',
        colors: {
          backgroundBarColors: ['#ddd']
        },
        borderRadius: 4,
      },
    },
    colors: colors[6],
    stroke: {
      width: 0,
      lineCap: 'round'
    },
    series: [{
      name: '슈퍼풀',
      data: [superbonus_percent]
    }],
    fill: {
      type: 'gradient',
      gradient: {
        gradientToColors: [colors[7]]
      }
    },
    title: {
      floating: true,
      offsetX: 0,
      offsetY: 0,
      text: super_title,
      style: {
        fontSize: '16px',
        color: title_color
      }
    },
    subtitle: {
      floating: true,
      align: 'right',
      offsetY: 15,
      offsetx: 10,
      text: sub_title2,
      style: {
        fontSize: '13px',
        color: '#999'
      }
    },
    dataLabels: {
        enabled: true,
        offsetX: 0,
        style: {
            fontSize: '16px'
        },
        formatter: function (val, opt) {
            return opt.w.globals.labels[0] + ":  " + val + "%"
        },
        
    },
    tooltip: {
      enabled: false
    },
    xaxis: {
      categories: [[bonus_title]],
    },
    yaxis: {
      max: 100
    },
  }
  var superchart = new ApexCharts(document.querySelector("#superchart"), super_chart);


  
// 마이

var my_spark1 = {
  chart: {
      id: 'my_sparkline1',
      type: 'area',
      height: 160,
      sparkline: {
        enabled: true
      },
    },
    stroke: {
      curve: 'straight'
    },
    fill: {
      opacity: 1,
    },
    series: [{
      name: 'My Mining Bonus', 
      data: dataset(miningData,'mining')
    }],
    labels: dataset(chartData,'date'),
      //labels: [...Array(5).keys()].map(n => `2020-09-0${n+1}`),
    colors: ['#DCE6EC'],
    title: {
      text: Price(super_hash_data[super_hash_data.length -1]),
      offsetX: 0,
      offsetY: 0,
      style: {
        fontSize: '24px',
        cssClass: 'apexcharts-yaxis-title'
      }
    },
    subtitle: {
      text: 'My Mining Bonus',
      offsetX: 0,
      offsetY: 30,
      style: {
        fontSize: '14px',
        cssClass: 'apexcharts-yaxis-title'
      }
    }
}
var chart_my_spark1 = new ApexCharts(document.querySelector("#myspark1"), my_spark1)


  /* var super_chart = {
    series: [
    {
      name: 'Actual',
      data: [
        {
          x: '2011',
          y: 12,
          goals: [
            {
              name: 'Expected',
              value: 14,
              strokeWidth: 2,
              strokeDashArray: 2,
              strokeColor: '#775DD0'
            }
          ]
        },
        {
          x: '2012',
          y: 44,
          goals: [
            {
              name: 'Expected',
              value: 54,
              strokeWidth: 5,
              strokeHeight: 10,
              strokeColor: '#775DD0'
            }
          ]
        },
        {
          x: '2013',
          y: 54,
          goals: [
            {
              name: 'Expected',
              value: 52,
              strokeWidth: 10,
              strokeHeight: 0,
              strokeLineCap: 'round',
              strokeColor: '#775DD0'
            }
          ]
        },
        {
          x: '2014',
          y: 66,
          goals: [
            {
              name: 'Expected',
              value: 61,
              strokeWidth: 10,
              strokeHeight: 0,
              strokeLineCap: 'round',
              strokeColor: '#775DD0'
            }
          ]
        },
        {
          x: '2015',
          y: 81,
          goals: [
            {
              name: 'Expected',
              value: 66,
              strokeWidth: 10,
              strokeHeight: 0,
              strokeLineCap: 'round',
              strokeColor: '#775DD0'
            }
          ]
        },
        {
          x: '2016',
          y: 67,
          goals: [
            {
              name: 'Expected',
              value: 70,
              strokeWidth: 5,
              strokeHeight: 10,
              strokeColor: '#775DD0'
            }
          ]
        }
      ]
    }
  ],
    chart: {
    height: 350,
    type: 'bar'
  },
  plotOptions: {
    bar: {
      horizontal: true,
    }
  },
  colors: ['#00E396'],
  dataLabels: {
    formatter: function(val, opt) {
      const goals =
        opt.w.config.series[opt.seriesIndex].data[opt.dataPointIndex]
          .goals
  
      if (goals && goals.length) {
        return `${val} / ${goals[0].value}`
      }
      return val
    }
  },
  legend: {
    show: true,
    showForSingleSeries: true,
    customLegendItems: ['Actual', 'Expected'],
    markers: {
      fillColors: ['#00E396', '#775DD0']
    }
  }
  };

  var chart = new ApexCharts(document.querySelector("#superchart"), super_chart);
  chart.render(); */