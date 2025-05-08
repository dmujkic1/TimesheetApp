<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative">
        <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-500 hover:text-black">✖</button>
        <div class="mb-4">
          <slot name="header" />
        </div>
        <div>
          <slot name="body" />
        </div>
        <div class="mt-6 text-right">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </template>
  