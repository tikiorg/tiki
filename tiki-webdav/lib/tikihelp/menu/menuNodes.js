// You can find instructions for this file here:
// http://www.treeview.net

// Decide if the names are links or just the icons
USETEXTLINKS = 1  //replace 0 with 1 for hyperlinks

// Decide if the tree is to start all open or just showing the root folders
STARTALLOPEN = 0 //replace 0 with 1 to show the whole tree
USEICONS = 1
ICONPATH = 'icons/' //change if the gif's folder is a subfolder, for example: 'images/'
PERSERVESTATE = 1


foldersTree = gFld("Index", "index.html")
foldersTreeA=insFld(foldersTree,gFld("Fuego Studio","javascript:void(0)"));
foldersTreeAA=insFld(foldersTreeA,gFld("Fuego Basics","javascript:void(0)"));
insDoc(foldersTreeAA,gLnk("R","Business Services Orchestration","pages/Doc_Business_Orchestration_Shared.html"));
insDoc(foldersTreeAA,gLnk("R","What's Fuego","pages/Doc_What_Fuego_Shared.html"));
insDoc(foldersTreeAA,gLnk("R","Introducing Fuego eXpress","pages/Doc_Why_Fuego_Shared.html"));
insDoc(foldersTreeAA,gLnk("R","System Requirements","pages/Doc_SystemRequirements_Shared.html"));
insDoc(foldersTreeAA,gLnk("R","Installing Fuego eXpress in the Production environment","pages/Doc_express_Shared.html"));
insDoc(foldersTreeAA,gLnk("R","Architecture","pages/Doc_Architecture_Shared.html"));
insDoc(foldersTreeAA,gLnk("R","Internationalization","pages/Doc_I18N_Shared.html"));
insDoc(foldersTreeAA,gLnk("R","Development Workspace","pages/Doc_Development_Workspace_Shared.html"));
insDoc(foldersTreeAA,gLnk("R","Contextual Help","pages/Doc_Contextual_Help_Shared.html"));
insDoc(foldersTreeA,gLnk("R","About this Documentation release - General Considerations","pages/Doc_Lanin_Doc_Considerations_Shared.html"));
insDoc(foldersTreeA,gLnk("R","Defining an Orchestration Project","pages/Doc_Defining_a_Project_st.html"));
foldersTreeA=insFld(foldersTree,gFld("Designing a Process","pages/Doc_Designing_a_Process_st.html"));
insDoc(foldersTreeA,gLnk("R","Instance","pages/Doc_Instance_st.html"));
foldersTreeAA=insFld(foldersTreeA,gFld("Process","pages/Doc_Process_st.html"));
insDoc(foldersTreeAA,gLnk("R","Process Group","pages/Doc_processgroup_st.html"));
insDoc(foldersTreeAA,gLnk("R","Process Exception Flow","pages/Doc_processexception_st.html"));
insDoc(foldersTreeAA,gLnk("R"," Importing a process from Visio","pages/Doc_Import_proc_Visio_st.html"));
insDoc(foldersTreeA,gLnk("R"," Roles within a Process","pages/Doc_RoleProc_st.html"));
foldersTreeAA=insFld(foldersTreeA,gFld("Activity","pages/Doc_Activity_st.html"));
foldersTreeAAA=insFld(foldersTreeAA,gFld("Initiating a Process","javascript:void(0)"));
insDoc(foldersTreeAAA,gLnk("R","Begin Activity","pages/Doc_activities_begin_st.html"));
insDoc(foldersTreeAAA,gLnk("R","End Activity","pages/Doc_activities_end_st.html"));
foldersTreeAAA=insFld(foldersTreeAA,gFld("Human Interaction","javascript:void(0)"));
insDoc(foldersTreeAAA,gLnk("R","Interactive","pages/Doc_activities_interactive_st.html"));
insDoc(foldersTreeAAA,gLnk("R","Grab.","pages/Doc_activities_grab_st.html"));
insDoc(foldersTreeAA,gLnk("R","L2","pages/byie.html"));
insDoc(foldersTreeA,gLnk("R","312","pages/sads.html"));
insDoc(foldersTree,gLnk("R","31231","pages/dassda.html"));
