<!-- resources/views/example.blade.php -->
<div class="p-4 max-w-md mx-auto">
  <div class="mb-4">
    <label for="htmlInput" class="block text-sm font-medium text-gray-700 mb-2">
      {{$getLabel()}}
    </label>
    <div class="flex flex-col gap-2 justify-center items-center border border-gray-300 rounded-lg p-4 max-h-[60px] bg-white">
      {!! $getContent() !!}
    </div>
  </div>
</div>
