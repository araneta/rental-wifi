#jav
export JAVA_HOME=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/jdk-21.0.1
export PATH=$JAVA_HOME/bin:$PATH

# Android SDK
export ANDROID_SDK=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/Android/Sdk
export PATH=$ANDROID_SDK:$PATH

export PATH=$ANDROID_SDK/tools:$PATH
export PATH=$ANDROID_SDK/tools/bin:$PATH
export PATH=$ANDROID_SDK/platform-tools:$PATH

# Flutter
export FLUTTER=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/flutter-3.7.12/flutter

export PATH=$FLUTTER/bin:$PATH
flutter config --android-sdk ANDROID_SDK
flutter devices
