<template>
  <div v-html="answer"></div>
</template>
<script>
import axios from 'axios';

export default {
  name: 'App',
  data() {
    return {
      answer: {},
    };
  },
  methods: {
    async getAnswer() {
      const { data } = await axios.get('http://192.168.32.133/systemstatus/index.php');
      this.answer = data;
    },
  },
  beforeMount() {
    this.getAnswer();
  },
};
</script>
