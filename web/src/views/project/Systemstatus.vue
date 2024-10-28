<template>
  <div id="app">
    <h1>Vue Apexcharts Demo</h1>
    <SystemStatus msg="Welcome to Your Vue.js App"/>
  </div>
</template>

<script>
import SystemStatus from '@/components/Systemstatus.vue';

export default {
  name: 'App',
  components: {
    SystemStatus,
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
