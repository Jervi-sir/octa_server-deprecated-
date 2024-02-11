import { useNavigation } from "@react-navigation/native"
import { StyleSheet, Text } from "react-native"
import { Image, View } from "react-native"
import { TouchableOpacity } from "react-native-gesture-handler"
import Colors from "@utils/Colors"
import Routes from "@utils/Routes"
import { BackIcon, StoreIcon } from "@components/icons/IconsInUsage"
import { LeftArrowSVG } from "@components/icons/GeneralIcons"
import { FriendListSVG,  ContactTeamSVG,  CopyProfileSVG,  LanguageSVG, LogoutSVG, AboutUsSVG, AccountSVG, SettingsSVG, EditProfileSVG, LikeListSVG, BlockedAccountsSVG } from '@components/icons/SettingsIcons'
export const MainSettingsList = () => {
  const navigation = useNavigation();

  return (
    <>
      <View style={{flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 20}}>
        <TouchableOpacity
          onPress={() => navigation.goBack()}
        >
          <LeftArrowSVG />
        </TouchableOpacity>
        <View style={{marginLeft: 10, flexDirection: 'row', alignItems: 'center'}}>
          <Text style={{fontSize: 15, fontWeight: '700'}}>General Settings</Text>
        </View>
        <View style={{width: 30}}></View>
      </View>
      <View style={{flex: 1, justifyContent: 'space-between', marginHorizontal: 20, marginTop: 10}}>
        <View >
          <TouchableOpacity 
            style={styles.setting}
            onPress={() => navigation.navigate(Routes.SettingEditProfile)}
          >
            <EditProfileSVG />
            <Text style={styles.settingText}>Edit Profile</Text>
          </TouchableOpacity>
          <Separator />
          <TouchableOpacity 
            style={styles.setting}
            onPress={() => navigation.navigate(Routes.SettingFriendsList)}
          >
            <FriendListSVG />
            <Text style={styles.settingText}>Friends List</Text>
          </TouchableOpacity>
          <Separator />
          <TouchableOpacity 
            style={styles.setting}
            onPress={() => navigation.navigate(Routes.SettingLikesList)}
          >
            <LikeListSVG />
            <Text style={styles.settingText}>Likes List</Text>
          </TouchableOpacity>
          <Separator />
          <TouchableOpacity 
            style={styles.setting}
            onPress={() => navigation.navigate(Routes.SettingBlockedAccounts)}
          >
            <BlockedAccountsSVG />
            <Text style={styles.settingText}>Blocked Accounts</Text>
          </TouchableOpacity>
          <Separator />
          <TouchableOpacity 
            style={styles.setting}
            onPress={() => navigation.navigate(Routes.SettingLanguages)}
          >
            <LanguageSVG />
            <Text style={styles.settingText}>Languages</Text>
          </TouchableOpacity>
          <Separator />
          <TouchableOpacity 
            style={styles.setting}
            onPress={() => navigation.navigate(Routes.SettingAboutUs)}
          >
            <AboutUsSVG />
            <Text style={styles.settingText}>About Us</Text>
          </TouchableOpacity>
          <Separator />
        </View>
     
        <View style={{marginBottom: 50}}>
          <TouchableOpacity>
            <LogoutSVG />
            <Text style={[styles.settingText, { color: 'red'}]}>Logout</Text>
          </TouchableOpacity>
        </View>
            
      </View>

    </>
  )
}

const styles = StyleSheet.create({
  setting: {
    flexDirection: 'row',
    alignItems: 'center'
  },
  settingText: {
    fontSize: 17, 
    fontWeight: '500',
    paddingLeft: 7
  }
})

const Separator = () => {
  return (
    <View style={{marginVertical: 20 , height: 2, backgroundColor: Colors.separator}}></View>
  )
}