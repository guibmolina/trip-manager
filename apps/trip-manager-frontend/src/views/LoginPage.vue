<script setup>
import LoginForm from '@/components/Login/LoginForm.vue'
</script>
<template>
  <div>
    <div class="w-full flex items-center justify-center flex-col gap-12">
      <div>
        <span class="md:text-6xl text-3xl font-semibold text-[#1d2b39]">Trip Manager</span>
      </div>
      <div class="w-full">
        <LoginForm></LoginForm>
      </div>
    </div>
  </div>
</template>
