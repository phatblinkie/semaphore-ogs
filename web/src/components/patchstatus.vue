<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title>Patch Status</v-toolbar-title>
    </v-toolbar>

    <v-tabs show-arrows class="pl-4">
      <v-tab
        key="Systemstatus"
        :to="`/project/${projectId}/systemstatus`"
      >
        System Status
      </v-tab>
      <v-tab
        key="TaskGraphs"
        :to="`/project/${projectId}/taskgraphs`"
      >
        Task Graphs
      </v-tab>
      <v-tab
        key="Patchstatus"
        :to="`/project/${projectId}/patchstatus`"
      >
        Patch Status
      </v-tab>
      <v-tab
        key="Compliancestatus"
        :to="`/project/${projectId}/compliancestatus`"
      >
        Compliance Status
      </v-tab>
    </v-tabs>

    <v-container class="align-left">
      <v-row>
        <v-col cols="3">
          <h2>Hosts and Available Updates</h2>
          <v-data-table
            :headers="headers"
            :items="hosts"
            item-key="hostname"
            dense
            hide-default-footer
            class="fixed-table"
          >
            <template v-slot:item.hostname="{ item }">
              <v-btn
                small
                outlined
                @click="onHostSelected(item.hostname)"
              >
                {{ item.hostname }}
              </v-btn>
            </template>
          </v-data-table>
        </v-col>

        <v-col cols="9">
          <h2>Host Details</h2>
          <div v-if="hostDetails">
            <v-simple-table dense class="fixed-table">
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
                      <a
                        :href="`/project/${projectId}/templates?t=${hostDetails.task_id}`"
                      >
                        {{ hostDetails.task_id }}
                      </a>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>

            <div style="display: flex; gap: 20px; margin-top: 20px;">
              <v-btn
                small
                outlined
                @click="toggleTable('available')"
              >
                Available Updates
              </v-btn>
              <v-btn
                small
                outlined
                @click="toggleTable('installed')"
              >
                Installed Updates
              </v-btn>
            </div>

            <div v-if="showAvailableUpdates" style="margin-top: 20px;">
              <v-simple-table dense class="fixed-table">
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">Available Updates</th>
                      <th>
                        <v-text-field
                          v-model="availableUpdatesSearch"
                          label="Search"
                          dense
                          hide-details
                          @keyup="searchAvailableUpdates"
                        ></v-text-field>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="filteredAvailableUpdates.length === 0">
                      <td colspan="2">No updates available</td>
                    </tr>
                    <tr
                      v-else
                      v-for="(updateItem, index) in filteredAvailableUpdates"
                      :key="index"
                    >
                      <td colspan="2">{{ updateItem }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </div>

            <div v-if="showInstalledUpdates" style="margin-top: 20px;">
              <v-simple-table dense class="fixed-table">
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">Installed Updates</th>
                      <th>
                        <v-text-field
                          v-model="installedUpdatesSearch"
                          label="Search"
                          dense
                          hide-details
                          @keyup="searchInstalledUpdates"
                        ></v-text-field>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="filteredInstalledUpdates.length === 0">
                      <td colspan="2">No installed updates</td>
                    </tr>
                    <tr
                      v-else
                      v-for="(installed, index) in filteredInstalledUpdates"
                      :key="index"
                    >
                      <td colspan="2">{{ installed }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </div>
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
      showAvailableUpdates: false,
      showInstalledUpdates: false,
      availableUpdatesSearch: '',
      installedUpdatesSearch: '',
      filteredAvailableUpdates: [],
      filteredInstalledUpdates: [],
      headers: [
        { text: 'Host', value: 'hostname' },
        {
          text: 'Available Updates',
          value: 'available_updates',
        },
      ],
    };
  },
  created() {
    this.fetchHosts();
  },
  methods: {
    async fetchHosts() {
      try {
        const resp = await axios.get(
          `/post/get_patch_status_hosts.php?project_id=${this.projectId}`,
        );
        this.hosts = resp.data;
      } catch (error) {
        console.error('Error fetching hosts:', error);
      }
    },

    async onHostSelected(hostname) {
      await this.fetchHostDetails(hostname);
    },

    async fetchHostDetails(hostname) {
      try {
        const resp = await axios.get(
          `/post/get_combined_patch_status.php?project_id=${this.projectId}&hostname=${hostname}`,
        );
        this.hostDetails = resp.data;
        this.showAvailableUpdates = false;
        this.showInstalledUpdates = false;
        this.filteredAvailableUpdates = this.hostDetails.updates || [];
        this.filteredInstalledUpdates = this.hostDetails.installedUpdates || [];
        // console.log('Host details:', this.hostDetails);
        // console.log('Filtered installed updates:', this.filteredInstalledUpdates);
      } catch (error) {
        console.error('Error fetching host details:', error);
      }
    },

    async searchAvailableUpdates() {
      if (!this.availableUpdatesSearch) {
        this.filteredAvailableUpdates = this.hostDetails.updates || [];
        return;
      }
      try {
        const resp = await axios.get(
          `/post/search_available_updates.php?project_id=${this.projectId}&hostname=${this.hostDetails.hostname}&search=${this.availableUpdatesSearch}`,
        );
        this.filteredAvailableUpdates = resp.data;
      } catch (error) {
        console.error('Error searching available updates:', error);
      }
    },

    async searchInstalledUpdates() {
      if (!this.installedUpdatesSearch) {
        // Ensure it's always an array
        this.filteredInstalledUpdates = Array.isArray(this.hostDetails.installedUpdates)
          ? this.hostDetails.installedUpdates
          : [];
        return;
      }
      try {
        const resp = await axios.get(
          `/post/search_installed_updates.php?project_id=${this.projectId}&hostname=${this.hostDetails.hostname}&search=${this.installedUpdatesSearch}`,
        );
        // console.log('Installed updates response:', resp.data);
        // Always store an array
        this.filteredInstalledUpdates = Array.isArray(resp.data)
          ? resp.data
          : [];
        // console.log('Filtered installed updates:', this.filteredInstalledUpdates);
      } catch (error) {
        // console.error('Error searching installed updates:', error);
      }
    },

    toggleTable(table) {
      if (table === 'available') {
        this.showAvailableUpdates = !this.showAvailableUpdates;
        this.showInstalledUpdates = false;
        this.installedUpdatesSearch = ''; // Reset search field
        this.filteredAvailableUpdates = this.hostDetails.updates || [];
      } else if (table === 'installed') {
        this.showInstalledUpdates = !this.showInstalledUpdates;
        this.showAvailableUpdates = false;
        this.availableUpdatesSearch = ''; // Reset search field
        this.filteredInstalledUpdates = this.hostDetails.installedUpdates || [];
      }
    },

    formatTimestamp(ts) {
      const dateObj = new Date(ts);
      return dateObj.toLocaleString();
    },
  },
  watch: {
    showAvailableUpdates(newVal) {
      if (newVal) {
        this.installedUpdatesSearch = ''; // Reset search field
      }
    },
    showInstalledUpdates(newVal) {
      if (newVal) {
        this.availableUpdatesSearch = ''; // Reset search field
      }
    },
    installedUpdatesSearch(newVal) {
      // If search is cleared, ensure we still have an array
      if (!newVal) {
        this.filteredInstalledUpdates = Array.isArray(this.hostDetails.installedUpdates)
          ? this.hostDetails.installedUpdates
          : [];
      }
    },
  },
};
</script>

<style scoped>
/*
  Forces a fixed minimal table width and avoids horizontal scroll:
  - min-width ensures the table won't shrink below that width.
  - overflow: hidden and white-space: normal let cells wrap text
    instead of scrolling.
*/
.fixed-table {
  min-width: 1200px;
  table-layout: fixed;
  border-collapse: collapse;
  overflow: hidden;
}
.fixed-table th,
.fixed-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
  white-space: normal;
  text-overflow: ellipsis;
  vertical-align: top;
}
/* Remove/hide horizontal scroll bars on smaller screens */
html,
body {
  overflow-x: hidden;
}
.fixed-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}
.align-left {
  margin-left: 0;
}
</style>
