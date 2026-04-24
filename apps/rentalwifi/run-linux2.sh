export JAVA_HOME=/mnt/projects1/apps/jdk-17.0.11
export PATH=$JAVA_HOME/bin:$PATH

export ANDROID=/mnt/projects1/apps/Android/Sdk
export PATH=$ANDROID/tools:$PATH
export PATH=$ANDROID/tools/bin:$PATH
export PATH=$ANDROID/platform-tools:$PATH
# Android SDK
export ANDROID_SDK=/mnt/projects1/apps/Android/Sdk
export ANDROID_SDK_ROOT=/mnt/projects1/apps/Android/Sdk
export PATH=$ANDROID_SDK:$PATH
export ANDROID_SDK_ROOT=/mnt/projects1/apps/Android/Sdk
export PATH="/home/araneta/fvm/bin:$PATH"
source ~/.bashrc 
fvm flutter build apk
