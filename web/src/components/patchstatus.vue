<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title>Patch Status</v-toolbar-title>
    </v-toolbar>
    <v-tabs show-arrows class="pl-4">
      <v-tab key="Systemstatus" :to="`/project/${projectId}/systemstatus`">System Status</v-tab>
      <v-tab key="TaskGraphs" :to="`/project/${projectId}/graphs`">Task Graphs</v-tab>
      <v-tab key="Patchstatus" :to="`/project/${projectId}/patchstatus`">Patch Status</v-tab>
      <v-tab key="Compliancestatus" :to="`/project/${projectId}/compliancestatus`">
        Compliance Status
      </v-tab>
    </v-tabs>
    <v-container>
      <v-row>
        <v-col cols="3">
          <h2>Hosts and Available Updates</h2>
          <v-data-table
            :headers="headers"
            :items="hosts"
            item-key="hostname"
            dense
            hide-default-footer
          >
            <template v-slot:item.hostname="{ item }">
              <v-btn small outlined @click="fetchHostDetails(item.hostname)">
                {{ item.hostname }}
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
        <v-col cols="9">
          <h2>Host Details</h2>
          <div v-if="hostDetails">
            <v-simple-table dense>
              <template v-slot:default>
                <tbody>
                  <tr>
                    <td><strong>Hostname</strong></td>
                    <td>{{ hostDetails.hostname }}</td>
                  </tr>
                  <tr>
                    <td><strong>Timestamp</strong></td>
                    <td>{{ formatTimestamp(hostDetails.timestamp) }}</td>
                  </tr>
                  <tr>
                    <td><strong>Failed Updates</strong></td>
                    <td>{{ hostDetails.failed }}</td>
                  </tr>
                  <tr>
                    <td><strong>Last Task Run</strong></td>
                    <td>
                      <a :href="`/project/${projectId}/templates?t=${hostDetails.task_id}`">
                        {{ hostDetails.task_id }}
                      </a>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <v-simple-table dense>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">Available Updates</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="hostDetails.updates.length === 0">
                    <td>No updates available</td>
                  </tr>
                  <tr v-else v-for="update in hostDetails.updates" :key="update">
                    <td>{{ update }}</td>
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
      headers: [
        { text: 'Host', value: 'hostname' },
        { text: 'Available Updates', value: 'available_updates' },
      ],
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
    formatTimestamp(timestamp) {
      const date = new Date(timestamp);
      return date.toLocaleString();
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

.col-3,
.col-9 {
  text-align: left;
}

.row {
  width: 100%;
}
</style>
