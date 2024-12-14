<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title>Patch Status</v-toolbar-title>
    </v-toolbar>
    <v-tabs show-arrows class="pl-4">
      <v-tab key="Systemstatus" :to="`/project/${projectId}/systemstatus`">System Status</v-tab>
      <v-tab key="Graphs" :to="`/project/${projectId}/graphs`">Graphs</v-tab>
      <v-tab key="Patchstatus" :to="`/project/${projectId}/patchstatus`">Patch Status</v-tab>
      <v-tab key="Compliancestatus" :to="`/project/${projectId}/compliancestatus`">
        Compliance Status
      </v-tab>
    </v-tabs>
    <v-container>
      <v-row>
        <v-col cols="4">
          <h2>Hosts</h2>
          <v-list dense>
            <v-list-item
              v-for="host in hosts"
              :key="host.hostname"
              @click="fetchHostDetails(host.hostname)"
            >
              <v-list-item-content>
                <v-list-item-title>{{ host.hostname }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-col>
        <v-col cols="8">
          <h2>Host Details</h2>
          <div v-if="hostDetails">
            <v-simple-table dense>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">Field</th>
                    <th class="text-left">Value</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>Hostname</strong></td>
                    <td>{{ hostDetails.hostname }}</td>
                  </tr>
                  <tr>
                    <td><strong>Changed</strong></td>
                    <td>{{ hostDetails.changed }}</td>
                  </tr>
                  <tr>
                    <td><strong>Failed</strong></td>
                    <td>{{ hostDetails.failed }}</td>
                  </tr>
                  <tr>
                    <td><strong>Message</strong></td>
                    <td>{{ hostDetails.msg }}</td>
                  </tr>
                  <tr>
                    <td><strong>Return Code</strong></td>
                    <td>{{ hostDetails.rc }}</td>
                  </tr>
                  <tr>
                    <td><strong>Project ID</strong></td>
                    <td>{{ hostDetails.project_id }}</td>
                  </tr>
                  <tr>
                    <td><strong>Task ID</strong></td>
                    <td>{{ hostDetails.task_id }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <h3>Results</h3>
            <v-simple-table dense>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">Result</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="result in hostDetails.results" :key="result">
                    <td>{{ result }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </div>
          <div v-else>
            <p>Select a host to see details.</p>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PatchStatus',
  props: {
    projectId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      hosts: [],
      hostDetails: null,
    };
  },
  created() {
    this.fetchHosts();
  },
  methods: {
    async fetchHosts() {
      try {
        const response = await axios.get(
          `/post/get_patch_status_hosts.php?project_id=${this.projectId}`,
        );
        this.hosts = response.data;
      } catch (error) {
        console.error('Error fetching hosts:', error);
      }
    },
    async fetchHostDetails(hostname) {
      try {
        const response = await axios.get(
          `/post/get_patch_status_host_details.php?project_id=${this.projectId}&hostname=${hostname}`,
        );
        this.hostDetails = response.data;
      } catch (error) {
        console.error('Error fetching host details:', error);
      }
    },
  },
};
</script>

<style scoped>
.container {
  width: 100%;
  padding: 12px;
  margin-right: auto;
  margin-left: 18px;
}

.host-list {
  list-style-type: none;
  padding: 0;
}

.host-list li {
  margin-bottom: 10px;
}

.host-list a {
  text-decoration: none;
  color: #42b983;
}

.host-list a:hover {
  text-decoration: underline;
  color: #2c3e50;
}

.col-4,
.col-8 {
  text-align: left;
}

.row {
  width: 100%;
}
</style>
