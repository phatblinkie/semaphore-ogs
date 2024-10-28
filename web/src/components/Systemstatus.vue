<template>
  <div>
    <div class="chart-wrapper">
      <apexchart
        width="800" type="bar"
        :options="options" :series="series">
      </apexchart>
    </div>
    <hr>
    <div class="chart-wrapper">
      <apexchart
        width="800" type="line"
        :options="options" :series="series">
      </apexchart>
    </div>
    <div class="chart-wrapper">
      <apexchart
        width="600" type="donut"
        :options="donutOptions" :series="donutSeries">
      </apexchart>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SystemStatus',
  data: () => ({
    options: {
      chart: {
        id: 'vuechart-example',
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      },
      title: {
        text: 'Ansible Task Success Rates',
        align: 'center',
        style: {
          fontSize: '20px',
        },
      },
      colors: ['#00897b', ' #DC143C'],
    },
    series: [
      {
        name: 'Success',
        data: [55, 62, 89, 66, 98, 72, 101, 75, 94, 120, 117, 139],
      },
      {
        name: 'Failure',
        data: [5, 6, 9, 22, 0, 7, 15, 25, 4, 1, 0, 21],
      },
    ],
    donutOptions: {
      labels: ['Redhat 9', 'Windows 10', 'Windows Server 2019', 'Cisco', 'Unknown'],
      colors: ['#FF4560', '#008FFB', '#00E396', '#775DD0', '#FEB019'],
      title: {
        text: 'OGS Operating Systems',
        align: 'center',
        style: {
          fontSize: '20px',
        },
      },
    },
    donutSeries: [44, 55, 41, 17, 15],
  }),
};
</script>

<style scoped>
div.chart-wrapper {
  display: flex;
  align-items: center;
  justify-content: center
}
</style>
