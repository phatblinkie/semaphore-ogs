<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
  <div>
    <v-toolbar flat>
      <v-toolbar-title> System Status </v-toolbar-title>
    </v-toolbar>
    <v-tabs show-arrows class="pl-4">
      <v-tab key="Systemstatus" :to="`/project/${projectId}/systemstatus`"
        >System Status</v-tab
      >
      <v-tab key="Graphs" :to="`/project/${projectId}/graphs`">Graphs</v-tab>
      <v-tab key="Patchstatus" :to="`/project/${projectId}/patchstatus`"
        >Patch Status</v-tab
      >
      <v-tab key="Compliancestatus" :to="`/project/${projectId}/compliancestatus`"
        >Compliance Status</v-tab
      >
    </v-tabs>
    <v-container>
      <v-data-table
        :headers="headers"
        :items="items"
        :items-per-page="20"
        class="elevation-1"
      >
      </v-data-table>
    </v-container>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      projectId: 1, // Replace this with actual project ID extraction logic if needed
      headers: [
        { text: 'Hostname', value: 'hostname' },
        { text: 'Ansible Ping', value: 'ansible_ping' },
        { text: 'Disk Usage', value: 'disk_capacity' },
        { text: 'Proc Usage', value: 'proc_usage' },
        { text: 'App Check', value: 'app_check' },
      ],
      items: [], // This will be populated with data from the JSON source
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios
        .get('/post/get_system_status.php') // Replace with your actual JSON source URL
        .then((response) => {
          this.items = response.data; // Assuming the JSON data is an array of objects
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
  display: flex;
  align-items: center;
  justify-content: left;
}
</style>
