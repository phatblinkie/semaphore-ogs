<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
  <div>
    <v-toolbar flat>
      <v-toolbar-title>Graphs</v-toolbar-title>
    </v-toolbar>
    <v-tabs show-arrows class="pl-4">
      <v-tab key="Systemstatus" :to="`/project/${projectId}/systemstatus`">System Status</v-tab>
      <v-tab key="Graphs" :to="`/project/${projectId}/graphs`">Task Graphs</v-tab>
      <v-tab key="Patchstatus" :to="`/project/${projectId}/patchstatus`">Patch Status</v-tab>
      <v-tab key="Compliancestatus" :to="`/project/${projectId}/compliancestatus`"
      >Compliance Status</v-tab>
    </v-tabs>

    <div class="chart-wrapper">
      <apexchart
        width="600"
        height="350"
        type="bar"
        :options="graph1options"
        :series="graph1series"
        :key="graph1Key"
      ></apexchart>
      <apexchart
        width="600"
        height="350"
        type="bar"
        :options="stackedBarChartOptions"
        :series="stackedseries"
        :key="stackedBarKey"
      ></apexchart>
    </div>
    <hr>
    <div class="chart-wrapper">
      <apexchart
        width="600"
        height="350"
        type="line"
        :options="linechartoptions"
        :series="lineseries"
        :key="lineKey"
      ></apexchart>
      <apexchart
        width="600"
        height="350"
        type="donut"
        :options="donutOptions"
        :series="donutSeries"
        :key="donutKey"
      ></apexchart>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Graphs',
  data() {
    return {
      projectId: null, // Initialize projectId
      graph1options: {
        chart: {
          id: 'vuechart-example1',
        },
        xaxis: {
          categories: [],
        },
        title: {
          text: 'Tasks last 6 months',
          align: 'center',
          style: {
            fontSize: '20px',
          },
        },
        colors: ['#00897b', '#FF4560', '#ffcf12'],
      },
      graph1series: [],
      graph1Key: 0, // Add a key to force re-render

      stackedBarChartOptions: {
        chart: {
          id: 'stacked-bar-chart',
          type: 'bar',
          stacked: true,
          stackType: '100%',
          toolbar: {
            show: true,
          },
          zoom: {
            enabled: false,
          },
        },
        xaxis: {
          type: 'datetime',
          categories: [],
          tickPlacement: 'between',
        },
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 10,
            borderRadiusApplication: 'end',
            borderRadiusWhenStacked: 'last',
          },
        },
        title: {
          text: 'Tasks last 8 days',
          align: 'center',
          style: {
            fontSize: '20px',
          },
        },
        legend: {
          position: 'bottom',
        },
        fill: {
          opacity: 1,
        },
        colors: ['#00897b', '#FF4560', '#ffcf12'],
      },
      stackedseries: [],
      stackedBarKey: 0, // Add a key to force re-render

      linechartoptions: {
        chart: {
          id: 'vuechart-example2',
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
        colors: ['#00897b', '#FF4560', '#00E396', '#775DD0', '#FEB019'],
      },
      lineseries: [],
      lineKey: 0, // Add a key to force re-render

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
      donutSeries: [],
      donutKey: 0, // Add a key to force re-render
    };
  },

  mounted() {
    this.getProjectIdFromUrl();
    this.fetchGraph1Series();
    this.fetchBarSeries();
    this.fetchLineSeries();
    this.fetchDonutSeries();
  },
  methods: {
    getProjectIdFromUrl() {
      // Get the current URL
      const url = window.location.href;
      // Split the URL into parts based on the '/'
      const parts = url.split('/');
      // Extract the projectId (assuming it is the 4th part in the URL structure)
      this.projectId = parts[4];
    },
    async fetchGraph1Series() {
      try {
        const response = await axios.get('/post/get_barchart_data.php');
        const data = response.data;
        if (data && data.dates && data.series) {
          this.graph1options.xaxis.categories = data.dates || [];
          this.graph1series = data.series || [];
          this.graph1Key += 1; // Update the key to force re-render
        } else {
          console.error('Invalid data format:', data);
        }
      } catch (error) {
        console.error('Error fetching chart data:', error);
      }
    },
    async fetchBarSeries() {
      try {
        const response = await axios.get('/post/get_7_date_task_results.php');
        const data = response.data;
        if (data && data.dates && data.series) {
          this.stackedBarChartOptions.xaxis.categories = data.dates || [];
          this.stackedseries = data.series || [];
          this.stackedBarKey += 1; // Update the key to force re-render
        } else {
          console.error('Invalid data format:', data);
        }
      } catch (error) {
        console.error('Error fetching chart data:', error);
      }
    },
    async fetchLineSeries() {
      try {
        const response = await axios.get('/post/graph-line.php');
        this.lineseries = response.data || [];
        this.lineKey += 1; // Update the key to force re-render
      } catch (error) {
        console.error('Error fetching lineseries data:', error);
      }
    },
    async fetchDonutSeries() {
      try {
        const response = await axios.get('/post/graph-donut.php');
        this.donutSeries = response.data || [];
        this.donutKey += 1; // Update the key to force re-render
      } catch (error) {
        console.error('Error fetching donutSeries data:', error);
      }
    },
  },
};
</script>

<style scoped>
.chart-wrapper {
  display: flex;
  align-items: center;
  justify-content: left;
}
</style>
