<template>
    <v-chip v-if="status" style="font-weight: bold;" :color="getStatusColor(status)">
      <v-icon left>{{ getStatusIcon(status) }}</v-icon>
      {{ humanizeStatus(status) }}
    </v-chip>
  </template>

<script>
export default {
  props: {
    status: String,
  },
  methods: {
    getStatusIcon(status) {
      switch (status) {
        case 'OK':
          return 'mdi-check-circle';
        case 'Failed':
          return 'mdi-alert-circle';
        default:
          return 'mdi-help-circle';
      }
    },
    humanizeStatus(status) {
      switch (status) {
        case 'OK':
          return 'OK';
        case 'Failed':
          return 'Failed';
        default:
          return 'Unknown';
      }
    },
    getStatusColor(status) {
      switch (status) {
        case 'OK':
          return 'success';
        case 'Failed':
          return 'error';
        default:
          return 'grey';
      }
    },
  },
};
</script>

  <style scoped>
  .v-chip {
    display: flex;
    align-items: center;
  }
  </style>
