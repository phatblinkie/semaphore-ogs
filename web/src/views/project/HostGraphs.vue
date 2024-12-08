<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title>Host Graphs</v-toolbar-title>
    </v-toolbar>
    <v-tabs show-arrows class="pl-4">
      <v-tab key="Systemstatus" :to="`/project/${projectId}/systemstatus`">System Status</v-tab>
      <v-tab key="Graphs" :to="`/project/${projectId}/graphs`">Graphs</v-tab>
      <v-tab key="Patchstatus" :to="`/project/${projectId}/patchstatus`">Patch Status</v-tab>
      <v-tab key="Compliancestatus" :to="`/project/${projectId}/compliancestatus`">
        Compliance Status
      </v-tab>
    </v-tabs>

    <h1>Graphs for Host: {{ hostname }}</h1>
    <v-container>
      <v-row>
        <v-btn @click="setTimeFrame('today')">Today</v-btn>
        <v-btn @click="setTimeFrame('week')">This Week</v-btn>
        <v-btn @click="setTimeFrame('month')">This Month</v-btn>
      </v-row>
      <v-row>
        <v-col>
          <apexchart
            type="line"
            :options="diskChartOptions"
            :series="diskSeries"
            :key="diskChartKey"
          ></apexchart>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <apexchart
            type="line"
            :options="procChartOptions"
            :series="procSeries"
            :key="procChartKey"
          ></apexchart>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <apexchart
            type="line"
            :options="uptimeChartOptions"
            :series="uptimeSeries"
            :key="uptimeChartKey"
          ></apexchart>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import axios from 'axios';
import VueApexCharts from 'vue-apexcharts';

export default {
  components: {
    apexchart: VueApexCharts,
  },
  data() {
    return {
      projectId: this.$route.params.projectId,
      hostname: this.$route.params.hostname,
      timeFrame: 'today', // Default time frame
      diskSeries: [
        {
          name: 'Disk Capacity',
          data: [],
        },
      ],
      procSeries: [
        {
          name: 'Proc Usage',
          data: [],
        },
      ],
      uptimeSeries: [
        {
          name: 'Uptime',
          data: [],
        },
      ],
      diskChartOptions: {
        chart: {
          height: 350,
          type: 'line',
        },
        xaxis: {
          categories: [],
        },
        stroke: {
          curve: 'smooth',
        },
        title: {
          text: 'Disk Capacity',
          align: 'left',
        },
      },
      procChartOptions: {
        chart: {
          height: 350,
          type: 'line',
        },
        xaxis: {
          categories: [],
        },
        stroke: {
          curve: 'smooth',
        },
        title: {
          text: 'Proc Usage',
          align: 'left',
        },
      },
      uptimeChartOptions: {
        chart: {
          height: 350,
          type: 'line',
        },
        xaxis: {
          categories: [],
        },
        stroke: {
          curve: 'smooth',
        },
        title: {
          text: 'Uptime',
          align: 'left',
        },
      },
      diskChartKey: 0, // Add a key to force re-render
      procChartKey: 0, // Add a key to force re-render
      uptimeChartKey: 0, // Add a key to force re-render
    };
  },
  mounted() {
    this.fetchData();
  },
  watch: {
    '$route.params': {
      handler() {
        this.projectId = this.$route.params.projectId;
        this.hostname = this.$route.params.hostname;
        this.fetchData();
      },
      immediate: true,
    },
  },
  methods: {
    setTimeFrame(timeFrame) {
      this.timeFrame = timeFrame;
      this.fetchData();
    },
    fetchData() {
      axios
        .get(`/post/get_host_history.php?project_id=${this.projectId}&hostname=${this.hostname}&time_frame=${this.timeFrame}`)
        .then((response) => {
          const data = response.data;
          const categories = data.map((entry) => entry.last_updated);
          this.diskChartOptions.xaxis.categories = categories;
          this.procChartOptions.xaxis.categories = categories;
          this.uptimeChartOptions.xaxis.categories = categories;
          this.diskSeries[0].data = data.map((entry) => parseFloat(entry.disk_capacity));
          this.procSeries[0].data = data.map((entry) => parseFloat(entry.proc_usage));
          this.uptimeSeries[0].data = data.map((entry) => parseFloat(entry.uptime));
          this.diskChartKey += 1; // Update the key to force re-render
          this.procChartKey += 1; // Update the key to force re-render
          this.uptimeChartKey += 1; // Update the key to force re-render
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
        });
    },
  },
};
</script>

<style scoped>
.chart-wrapper {
  height: 400px;
}
</style>
